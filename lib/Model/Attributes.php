<?php
/**
 * Created by PhpStorm.
 * Date: 7/7/17
 */

namespace OK\Model;

/**
 * Class Attributes. Attributes can be accessed by class property on the key.
 * @package OK\Model
 * @getter
 */
class Attributes extends JSONObject implements \JsonSerializable
{

    /**
     * @param $key
     * @return Attribute
     */
    public function get($key) {
        return $this->{$key};
    }

    protected function child($key) {
        if (is_int($key)) {
            return new Attribute;
        }
        return new JSONObject;
    }

    /**
     * Set key the value key
     * @param string $key
     * @param mixed $value
     * @return string
     */
    protected function customKey($key, $value) {
        return $value->key;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize() {
        return array_values(get_object_vars($this)); // strip keys from all vars
    }

    protected function isList() {
        return true;
    }

    /**
     * Initialize from list of attributes.
     * @param Attribute[] $attributes
     * @return Attributes
     */
    public static function init(array $attributes) {
        $obj = new Attributes;
        foreach ($attributes as $attribute) {
            $obj->{$attribute->key} = $attribute;
        }
        return $obj;
    }

}