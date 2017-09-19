<?php
/**
 * Created by PhpStorm.
 * Date: 8/16/17
 */

namespace Okitcom\OkLibMagento\Block;


use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order;
use OK\Model\Network\Exception\NetworkException;

class OkCashPayment extends AbstractMethod
{
    const CODE = "okcheckout";
    const KEY_OK_TRANSACTION_ID = "ok_transaction_id";

    protected $_code = self::CODE;

    protected $_canCapture = true;

    protected $_canRefund = true;

    protected $_canRefundInvoicePartial = true;

//    protected $_isInitializeNeeded = true;
//
//    public function initialize($paymentAction, $stateObject) {
//        $stateObject["state"] = Order::STATE_COMPLETE;
//        return $this;
//    }

    public function canUseForCurrency($currencyCode) {
        $supported = [
            "EUR"
        ];
        return in_array($currencyCode, $supported);
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null) {
        // check if an OK checkout record exists
        return parent::isAvailable($quote) && $this->getHelper()->getByQuote($quote->getId()) != null;
    }

    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount) {
        /** @var Order $order */
        $order = $payment->getOrder();
        $checkout = $this->getHelper()->getByQuote($order->getQuoteId());
        $service = $this->getHelper()->getCashService();

        try {
            $service->refund($checkout->getGuid(), \OK\Model\Amount::fromEuro($amount));
        } catch (NetworkException $exception) {
            throw new LocalizedException(
                __("Unable to refund (amount: " . $amount . "): " . $exception->getMessage()),
                $exception);
        }
        return $this;
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount) {
        // this is basically a formality
        /** @var Order $order */
        $order = $payment->getOrder();
        $checkout = $this->getHelper()->getByQuote($order->getQuoteId());
        if ($checkout->getState() != "ClosedAndCaptured") {
            throw new LocalizedException(__("OK transaction state is invalid."));
        }
        //$payment->setAmount($checkout);
        $payment->setTransactionAdditionalInfo(
                self::KEY_OK_TRANSACTION_ID,
                $checkout->getOkTransactionId());
        $payment->setTransactionId($checkout->getOkTransactionId()
            )->setParentTransactionId($checkout->getOkTransactionId());

        return $this;
    }


    /**
     * @var \Okitcom\OkLibMagento\Helper\CheckoutHelper
     */
    private $checkoutHelper;

    /**
     * @return mixed|\Okitcom\OkLibMagento\Helper\CheckoutHelper
     */
    protected function getHelper() {
        if ($this->checkoutHelper == null) {
            //Get Object Manager Instance
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->checkoutHelper = $objectManager->create('Okitcom\OkLibMagento\Helper\CheckoutHelper');
        }
        return $this->checkoutHelper;
    }


}