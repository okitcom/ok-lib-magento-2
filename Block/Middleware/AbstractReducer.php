<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;


use Magento\Quote\Model\Quote;
use OK\Model\Cash\Transaction;

abstract class AbstractReducer
{

    abstract function execute(Quote $quote, Transaction $response);

}