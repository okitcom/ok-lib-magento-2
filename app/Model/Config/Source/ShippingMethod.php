<?php
/**
 * Created by PhpStorm.
 * Date: 8/15/17
 */

namespace Okitcom\OkLibMagento\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;

class ShippingMethod implements ArrayInterface
{

    protected $shipconfig;

    protected $scopeConfig;

    /**
     * ShippingMethod constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shipconfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shipconfig
    ) {
        $this->shipconfig = $shipconfig;
        $this->scopeConfig = $scopeConfig;
    }

    public function getShippingMethods() {

        $activeCarriers = $this->shipconfig->getActiveCarriers();
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $options = array();
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode . '_' . $methodCode;
                    $options[] = array('value' => $code, 'label' => $method);

                }
                $carrierTitle = $this->scopeConfig->getValue('carriers/' . $carrierCode . '/title');

            }
            $methods[] = array('value' => $options, 'label' => $carrierTitle);
        }
        return $methods;

    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray() {
        return $this->getShippingMethods();
    }
}