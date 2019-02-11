<?php
/**
 * Created by PhpStorm.
 * Date: 9/15/17
 */

namespace OK\Builder;


abstract class GenericBuilder
{

    protected $object;

    abstract protected function getObject();

    /**
     * GenericBuilder constructor.
     */
    public function __construct() {
        $this->object = $this->getObject();
    }

    public function __call($name, $arguments) {
        if (substr($name, 0, 3) === "set") {
            // setter
            $fieldName = strtolower(substr($name, 3, 1)) . substr($name, 4);
            if (array_key_exists($fieldName, get_object_vars($this->object))) {
                $this->object->{$fieldName} = $arguments[0];
            } else {
                throw new BuilderException("Field \"" . $fieldName . "\" does not exist on object of type " . get_class($this));
            }
        }
        return $this;
    }

    public function build() {
        return $this->object;
    }

}