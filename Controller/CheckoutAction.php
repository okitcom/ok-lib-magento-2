<?php
/**
 * Created by PhpStorm.
 * Date: 8/12/17
 */

namespace Okitcom\OkLibMagento\Controller;


use Magento\Framework\App\Action\Action;
use Magento\Quote\Model\Quote;
use OK\Model\Amount;
use OK\Model\Attribute;
use OK\Model\Attributes;
use OK\Model\Cash\LineItem;
use OK\Model\Cash\TransactionRequest;
use Okitcom\OkLibMagento\Setup\InstallData;

abstract class CheckoutAction extends Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Okitcom\OkLibMagento\Model\CheckoutFactory
     */
    protected $checkoutFactory;

    /**
     * @var \Okitcom\OkLibMagento\Helper\ConfigHelper
     */
    protected $configHelper;

    /**
     * @var \Okitcom\OkLibMagento\Helper\CheckoutHelper
     */
    protected $checkoutHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Okitcom\OkLibMagento\Helper\QuoteHelper
     */
    protected $quoteHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Checkout\Model\Session $session
     * @param \Okitcom\OkLibMagento\Model\CheckoutFactory $checkoutFactory
     * @param \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper
     * @param \Okitcom\OkLibMagento\Helper\CheckoutHelper $checkoutHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Okitcom\OkLibMagento\Helper\QuoteHelper $quoteHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $session,
        \Okitcom\OkLibMagento\Model\CheckoutFactory $checkoutFactory,
        \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper,
        \Okitcom\OkLibMagento\Helper\CheckoutHelper $checkoutHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Okitcom\OkLibMagento\Helper\QuoteHelper $quoteHelper
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->session = $session;
        $this->checkoutFactory = $checkoutFactory;
        $this->configHelper = $configHelper;
        $this->checkoutHelper = $checkoutHelper;
        $this->customerSession = $customerSession;
        $this->quoteHelper = $quoteHelper;
        parent::__construct($context);
    }

    /**
     * Process a quote into an OK request. Performs all necessary operations in Magento backend.
     * @param Quote|null $quote the quote to process. Leave null to get the current session quote, if any.
     * @return array|null
     */
    protected function requestCash(Quote $quote = null) {
        // get data
        if ($quote == null) {
            $quote = $this->session->getQuote();
        }
        if ($quote == null) {
            return null;
        }
//            $shippingAddress = $quote->getShippingAddress();
//            $shippingAddress->setShippingMethod($this->configHelper->getGeneralConfig("default_shipping_method"))
//                ->setCollectShippingRates(true)
//                ->collectShippingRates();
//            print_r($shippingAddress->getShippingRatesCollection());
//            die();
        $totalAmount = $quote->getBaseGrandTotal();
        if ($totalAmount > 0) {

            // create object
            $ok = $this->checkoutHelper->getCashService();

            $attributes = new Attributes();
            $attributes->name = Attribute::create("name", "Name", Attribute::TYPE_NAME, true);
            $attributes->email = Attribute::create("email", "Email", Attribute::TYPE_EMAIL, true);
            $attributes->address = Attribute::create("address", "Address", Attribute::TYPE_ADDRESS, true);
            $attributes->phone = Attribute::create("phone", "Phone", Attribute::TYPE_PHONENUMBER, true);
            if ($this->configHelper->getCheckoutConfig("ask_note")) {
                // Ask user for a note
                $attributes->note = Attribute::create("note", "Note", Attribute::TYPE_STRING, false);
            }

            $initiation = false;

            $request = new TransactionRequest();
            $request->reference = $quote->getId();
            $request->amount = Amount::fromEuro($totalAmount);
            $request->attributes = $attributes;
            $request->permissions = ["TriggerPaymentInitiation"];
            if ($this->customerSession->isLoggedIn()) {
                $token = $this->customerSession->getCustomer()->getData(InstallData::OK_TOKEN);
                if ($token != null) {
                    $request->initiationToken = $token;
                    $initiation = true;
                }
            }

            $lineItems = [];
            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($quote->getAllItems() as $item) {
//                    echo  $item->getQty() . " " . $item->getName() . " " . $item->getPrice() . " - " . $item->getDiscountAmount() . " Calc: " . Amount::fromEuro($item->getPrice() - ($item->getDiscountAmount() / $item->getQty()))->getEuro() . "\n";
                $lineItems[] = LineItem::create(
                    $item->getQty(),
                    $item->getSku(),
                    $item->getName(),
                    Amount::fromEuro(($item->getPrice() - ($item->getDiscountAmount() / $item->getQty()))),
                    0, // TODO: TAX
                    "EUR"
                );
            }
            $request->lineItems = $lineItems;

            $response = $ok->request($request);

            $checkout = $this->checkoutFactory->create();
            $checkout->setGuid($response->guid);
            $checkout->setQuoteId($quote->getId());
            $checkout->setOkTransactionId($response->id);
            $checkout->setState($response->state);
            $checkout->save();

            return [
                "guid" => $response->guid,
                "initiation" => $initiation
            ];
        }

        return null;
    }

    protected function json(array $data, $responseCode = 200) {
        $result = $this->resultJsonFactory->create();
        $result->setData($data);
        $result->setHttpResponseCode($responseCode);
        return $result;
    }

    protected function getOkLibEnvironment() {
        return $this->configHelper->getOkLibEnvironment();
    }

    protected function getLocale() {
        return $this->configHelper->getLocale();
    }

}