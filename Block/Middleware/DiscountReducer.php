<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;


use Magento\Quote\Model\Quote;
use OK\Model\Amount;
use OK\Model\Cash\Transaction;

class DiscountReducer extends AbstractReducer
{

    function execute(Quote $quote, Transaction $response) {

//        $items = $response->lineItems->all();
//        $total = Amount::fromCents(0);
//        foreach ($items as $item) {
//            $total = $total->add($item->totalAmount);
//        }
//        //die($total->getEuro());
//
//        //print_r($response);
//        //die($quote->getGrandTotal() . " " . $total->getCents());
//        $quote->setGrandTotal($total->getEuro());
//        $quote->setBaseGrandTotal($total->getEuro());
//        $quote->save();
    }
}