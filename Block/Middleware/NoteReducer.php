<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;


use Magento\Quote\Model\Quote;
use OK\Model\Amount;
use OK\Model\Cash\TransactionResponse;

class NoteReducer extends AbstractReducer
{

    function execute(Quote $quote, TransactionResponse $response) {
        $note = $response->attributes->get("note");
        if ($note != null) {
            $quote->setCustomerNote($note->value);
            $quote->save();
        }
    }
}