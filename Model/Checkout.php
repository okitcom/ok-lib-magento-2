<?php
/**
 * Created by PhpStorm.
 * Date: 8/12/17
 */

namespace Okitcom\OkLibMagento\Model;


use Magento\Framework\Model\AbstractModel;

class Checkout extends AbstractModel
{


    /**
     * Checkout constructor.
     */
    public function _construct() {
        $this->_init('Okitcom\OkLibMagento\Model\Resource\Checkout');
    }
}