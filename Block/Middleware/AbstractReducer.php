<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;


use Magento\Quote\Model\Quote;
use OK\Model\Cash\TransactionResponse;

abstract class AbstractReducer
{

    abstract function execute(Quote $quote, TransactionResponse $response);

}