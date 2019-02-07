<?php
/**
 * Created by PhpStorm.
 * Date: 8/12/17
 */

namespace Okitcom\OkLibMagento\Model\Resource;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Checkout extends AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct() {
        $this->_init("ok_checkout", "id");
    }
}