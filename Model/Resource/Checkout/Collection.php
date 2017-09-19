<?php
/**
 * Created by PhpStorm.
 * Date: 8/12/17
 */

namespace Okitcom\OkLibMagento\Model\Resource\Checkout;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Okitcom\OkLibMagento\Model\Checkout',
            'Okitcom\OkLibMagento\Model\Resource\Checkout'
        );
    }

}