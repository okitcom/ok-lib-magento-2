<?php
/**
 * Created by PhpStorm.
 * Date: 8/28/17
 */

namespace Okitcom\OkLibMagento\Block\Button;

use Magento\Catalog\Block\Product\View;
use Okitcom\OkLibMagento\Helper\ConfigHelper;
use Magento\Catalog\Block\Product\Context;

class Buynow extends View
{

    public function getProductId() {
        return $this->getProduct()->getEntityId();
    }

    public function getProductQty() {
        return 1;
    }

}