<?php
/**
 * Created by PhpStorm.
 * Date: 8/15/17
 */

namespace Okitcom\OkLibMagento\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;

class Environment implements ArrayInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray() {
        return [
            [
                "value" => 'development',
                "label" => 'Development'
            ],
            [
                "value" => 'production',
                "label" => 'Production'
            ],
        ];
    }
}