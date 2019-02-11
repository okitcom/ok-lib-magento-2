<?php
/**
 * Created by PhpStorm.
 * Date: 9/18/17
 */

namespace OK\Builder\Extensions;

use OK\Model\Cash\LineItem;
use OK\Model\Cash\LineItems;

trait BuildsLineItems
{

    /**
     * @param LineItem $lineItem
     * @return $this
     */
    public function addLineItem(LineItem $lineItem) {
        if (!isset($this->object->lineItems)) {
            $this->object->lineItems = LineItems::init([]);
        }
        $this->object->lineItems->add($lineItem);
        return $this;
    }

}