<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;


use Magento\Quote\Model\Quote;
use OK\Model\Cash\Transaction;

class ShippingReducer extends AbstractReducer
{

    /**
     * @var \Okitcom\OkLibMagento\Helper\ConfigHelper
     */
    private $configHelper;

    /**
     * ShippingReducer constructor.
     * @param \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper
     */
    public function __construct(\Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper) {
        $this->configHelper = $configHelper;
    }

    function execute(Quote $quote, Transaction $response) {
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod($this->configHelper->getCheckoutConfig("default_shipping_method"));
    }
}