<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;


use Magento\Quote\Model\Quote;
use OK\Model\Amount;
use OK\Model\Cash\Transaction;

class NoteReducer extends AbstractReducer
{

    function execute(Quote $quote, Transaction $response) {
        if (isset($response->attributes->note)) {
            $note = $response->attributes->get("note");
            $quote->setCustomerNote($note->value);
            $quote->save();
        }
    }
}