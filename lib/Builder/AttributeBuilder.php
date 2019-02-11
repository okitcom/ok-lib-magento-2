<?php
/**
 * Created by PhpStorm.
 * Date: 9/15/17
 */

namespace OK\Builder;


use OK\Model\Attribute;

/**
 * Class AttributeBuilder
 * @package OK\Builder
 *
 * @api
 * @method AttributeBuilder setKey(string $key)
 * @method AttributeBuilder setLabel(string $label)
 * @method AttributeBuilder setType(string $type)
 * @method AttributeBuilder setRequired(bool $required)
 * @method AttributeBuilder setVerified(bool $verified)
 * @method AttributeBuilder setSuggestedValue(string $suggestedValue)
 */
class AttributeBuilder extends GenericBuilder
{
    function getObject() {
        return new Attribute;
    }
}