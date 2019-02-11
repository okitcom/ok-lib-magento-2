<?php
/**
 * Created by PhpStorm.
 * Date: 9/18/17
 */

namespace OK\Builder\Extensions;


use OK\Model\Attribute;
use OK\Model\Attributes;

trait BuildsAttributes
{

    /**
     * @param Attribute $attribute to add
     * @return $this
     */
    public function addAttribute(Attribute $attribute) {
        if (!isset($this->object->attributes)) {
            $this->object->attributes = Attributes::init([]);
        }
        $this->object->attributes->{$attribute->key} = $attribute;
        return $this;
    }

}