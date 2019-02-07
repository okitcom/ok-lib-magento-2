<?php
/**
 * Created by PhpStorm.
 * Date: 8/14/17
 */

namespace Okitcom\OkLibMagento\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;
use OK\Model\Cash\Transaction;
use Okitcom\OkLibMagento\Block\Middleware\AddressReducer;
use Okitcom\OkLibMagento\Block\Middleware\CustomerReducer;
use Okitcom\OkLibMagento\Block\Middleware\DiscountReducer;
use Okitcom\OkLibMagento\Block\Middleware\LineItemReducer;
use Okitcom\OkLibMagento\Block\Middleware\NoteReducer;
use Okitcom\OkLibMagento\Block\Middleware\PaymentReducer;
use Okitcom\OkLibMagento\Block\Middleware\ShippingReducer;

class QuoteHelper extends AbstractHelper
{
    private $_storeManager;
    private $_product;
    private $_formkey;
    private $quote;
    private $quoteManagement;
    private $customerFactory;
    private $customerRepository;
    private $orderService;
    private $searchCriteriaBuilder;
    private $filterBuilder;
    private $configHelper;
    private $cartManagementInterface;
    private $cartRepositoryInterface;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    private $objectFactory;

    private $reducers = [
        CustomerReducer::class,
        AddressReducer::class,
        ShippingReducer::class,
        PaymentReducer::class,
        LineItemReducer::class,
        DiscountReducer::class,
        NoteReducer::class
    ];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Data\Form\FormKey $formKey $formkey,
     * @param \Magento\Quote\Model\Quote $quote,
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory,
     * @param \Magento\Sales\Model\Service\OrderService $orderService,
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Framework\DataObject\Factory $objectFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->configHelper = $configHelper;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->objectFactory = $objectFactory;
        parent::__construct($context);
    }


    /**
     * Create Order On Your Store
     *
     * @param Quote $quote
     * @param Transaction $response
     * @return OrderInterface
     */
    public function createOrder(Quote $quote, Transaction $response) {
        $quote->setCurrency();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($this->reducers as $reducer) {
            $f = $objectManager->create($reducer);
            $f->execute($quote, $response);
        }

        // Collect Rates and Set Shipping & Payment Method

        // Collect Totals & Save Quote
        $quote->collectTotals()->save();


        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);


        $order->setEmailSent(0);
        $increment_id = $order->getRealOrderId();
        if($order->getEntityId()){
            return $order;
        }
        return null;
    }

    /**
     * Create a new quote. Used for single instant checkout.
     * @param $products array of products with items with keys: product_id, price and qty
     * @return Quote
     */
    public function createQuote($products) {
        $cart_id = $this->cartManagementInterface->createEmptyCart();
        /** @var Quote $quote */
        $quote = $this->cartRepositoryInterface->get($cart_id);
        $quote->setStore($this->_storeManager->getStore()); //set store for which you create quote

        foreach ($products as $item) {
            $product = $this->_product->load($item['product_id']);

            $quote->addProduct(
                $product,
                $this->objectFactory->create($item['request'])
            );
        }
        $quote->setTotalsCollectedFlag(false)
            ->collectTotals()
            ->save();

        return $quote;
    }

    /**
     * Create empty quote object
     * @return Quote fresh quote object
     */
    public function createEmptyQuote() {
        return $this->quote->create();
    }

}