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

    /**
     * @var \Okitcom\OkLibMagento\Helper\CheckoutHelper
     */
    private $checkoutHelper;

    public function canUseForCurrency($currencyCode)
    {
        $supported = [
            "EUR",
        ];
        return in_array($currencyCode, $supported);
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        // check if an OK checkout record exists
        return parent::isAvailable($quote) && $this->getHelper()->getByQuote($quote->getId()) != null;
    }

    /**
     * @return mixed|\Okitcom\OkLibMagento\Helper\CheckoutHelper
     */
    protected function getHelper()
    {
        if ($this->checkoutHelper == null) {
            //Get Object Manager Instance
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->checkoutHelper = $objectManager->create('Okitcom\OkLibMagento\Helper\CheckoutHelper');
        }
        return $this->checkoutHelper;
    }

    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
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

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        // this is basically a formality
        /** @var Order $order */
        $order = $payment->getOrder();
        $checkout = $this->getHelper()->getFinalByQuote($order->getQuoteId());
        if ($checkout == null) {
            throw new LocalizedException(__("OK transaction state is invalid."));
        }

        // Match amounts
        $service = $this->getHelper()->getCashService();
        $okTransaction = $service->get($checkout->getGuid());

        $paidAmount = $okTransaction->authorisationResult->amount->getEuro();
        if ($paidAmount != $amount) {
            $this->_logger->error("OK transaction amount was not equal to the transaction.",
                [
                    "ok_transaction_amount" => $okTransaction->amount->getEuro(),
                    "capture_amount" => $amount,
                ]
            );
            throw new LocalizedException(__("Transaction could not be processed."));
        }

        $payment->setTransactionAdditionalInfo(
            self::KEY_OK_TRANSACTION_ID,
            $checkout->getOkTransactionId());
        $payment->setTransactionId($checkout->getOkTransactionId()
        )->setParentTransactionId($checkout->getOkTransactionId());

        return $this;
    }
}
