<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;


use Magento\Quote\Model\Quote;
use OK\Model\Cash\Transaction;
use Okitcom\OkLibMagento\Block\OkCashPayment;

class PaymentReducer extends AbstractReducer
{

    function execute(Quote $quote, Transaction $response) {
        $quote->setPaymentMethod(OkCashPayment::CODE); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory
        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => OkCashPayment::CODE]);
    }
}