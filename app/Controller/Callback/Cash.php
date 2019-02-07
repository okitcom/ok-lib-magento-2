<?php
/**
 * Created by PhpStorm.
 * Date: 8/11/17
 */

namespace Okitcom\OkLibMagento\Controller\Callback;


use Magento\Checkout\Model\Cart;
use OK\Model\Network\Exception\NetworkException;
use Okitcom\OkLibMagento\Controller\CheckoutAction;
use Okitcom\OkLibMagento\Model\Checkout;
use Okitcom\OkLibMagento\Model\CheckoutFactory;
use Okitcom\OkLibMagento\Model\Resource\Checkout\Collection;

class Cash extends CheckoutAction {

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var Collection
     */
    protected $checkoutCollection;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * Constructor
     *
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Checkout\Model\Session $session
     * @param CheckoutFactory $checkoutFactory
     * @param Collection $checkoutCollection
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Okitcom\OkLibMagento\Helper\QuoteHelper $quoteHelper
     * @param \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper
     * @param \Okitcom\OkLibMagento\Helper\CheckoutHelper $checkoutHelper
     * @param \Magento\Framework\Math\Random $mathRandom
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $session,
        \Okitcom\OkLibMagento\Model\CheckoutFactory $checkoutFactory,
        \Okitcom\OkLibMagento\Model\Resource\Checkout\Collection $checkoutCollection,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Okitcom\OkLibMagento\Helper\QuoteHelper $quoteHelper,
        \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper,
        \Okitcom\OkLibMagento\Helper\CheckoutHelper $checkoutHelper,
        \Magento\Framework\Math\Random $mathRandom
    ) {
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutCollection = $checkoutCollection;
        parent::__construct($context, $resultJsonFactory, $session, $checkoutFactory, $configHelper, $checkoutHelper, $customerSession, $quoteHelper, $mathRandom);
    }


    public function execute() {
        $externalId = $this->getRequest()->getParam("transaction");

        if ($externalId != null) {
            /** @var Checkout $checkout */
            $checkout = $this->checkoutHelper->getByExternalId($externalId);
            try {
                $okresponse = $this->checkoutHelper->getCashService()->get($checkout->getGuid());
            } catch (NetworkException $exception) {
                $redirect = $this->resultRedirectFactory->create();
                $redirect->setPath( 'checkout/onepage/failure');
                $this->messageManager->addErrorMessage(__(
                    "OK Cash Status NOTFOUND"
                ));
                return $redirect;
            }
            if ($checkout != null) {
                // get status
                $checkout->setState($okresponse->state);
                $checkout->save();
                if (isset($okresponse->authorisationResult) && $okresponse->authorisationResult->result == "OK") {
                    // process
                    if ($checkout->getSalesOrderId() == null) {
                        // update
                        $quote = $this->quoteRepository->get($checkout->getQuoteId());
                        $order = $this->quoteHelper->createOrder($quote, $okresponse);

                        $this->session->setLastQuoteId($quote->getId());
                        $this->session->setLastQuoteId($quote->getId());
                        $this->session->setLastSuccessQuoteId($quote->getId());
                        $this->session->setLastOrderId($order->getId());
                        $this->session->setLastRealOrderId($order->getIncrementId());
                        $this->session->setLastOrderStatus($order->getStatus());

                        $checkout->setSalesOrderId($order->getEntityId());
                        $checkout->save();
                    }

                    $this->cart->truncate();

                    $redirect = $this->resultRedirectFactory->create();
                    $redirect->setPath( 'checkout/onepage/success');
                    $this->messageManager->addSuccessMessage(__(
                        "OK Cash Status OK"
                    ));
                    return $redirect;
                }
            }
        }

        // Show a checkout error
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath( 'checkout/onepage/failure');
        if (isset($okresponse) && isset($okresponse->authorisationResult)) {
            $this->messageManager->addErrorMessage(__(
                "OK Cash Status " . $okresponse->authorisationResult->result
            ));
        }
        return $redirect;
    }
}