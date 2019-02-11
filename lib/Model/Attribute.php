<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Model;


class Attribute extends JSONObject
{

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string Attribute type, as defined in Attribute::TYPE_{*}
     */
    public $type;

    /**
     * @var bool whether the attribute is required.
     */
    public $required;

    /**
     * @var bool whether the value should be verified (in case of type EMAILADDRESS).
     */
    public $verified;

    /**
     * @var string will be set when the user has completed the request.
     */
    public $value;

    /**
     * @var string (optional) Semicolon separated list of options. Required for SELECT
     */
    public $suggestedValue;

    const ADDRESS_STREET = 0;
    const ADDRESS_NUMBER = 1;
    const ADDRESS_LINE2 = 2;
    const ADDRESS_LINE3 = 3;
    const ADDRESS_ZIP = 4;
    const ADDRESS_CITY = 5;
    const ADDRESS_COUNTRY = 6;

    /**
     * Get a part of the address attribute.
     * @param $component int component constant
     * @return string part
     */
    public function addressComponent($component) {
        return explode(", ", $this->value)[$component];
    }

    const FIRSTNAME = 0;
    const LASTNAME = 1;

    /**
     * Get a part of the name attribute.
     * @param $component int component constant
     * @return string part
     */
    public function nameComponent($component) {
        return explode(", ", $this->value)[$component];
    }

    const TYPE_STRING = "STRING";
    const TYPE_BOOLEAN = "BOOLEAN";
    const TYPE_INTEGER = "INTEGER";
    const TYPE_SELECT = "SELECT";
    /**
     * A token that the service can use to send requests to the users app without the QR code scan
     */
    const TYPE_TOKEN = "TOKEN";
    const TYPE_NAME = "NAME";
    const TYPE_EMAIL = "EMAILADDRESS";
    const TYPE_PHONENUMBER = "PHONENUMBER";
    /**
     * The location of the device
     */
    const TYPE_LOCATION = "LOCATION";
    /**
     * One of the (verified) addresses of the user
     */
    const TYPE_ADDRESS = "ADDRESS";

    /**
     * @param $key
     * @param $label
     * @param $type
     * @param $required
     * @param $value
     * @param null $suggestedValue
     * @return Attribute
     */
    public static function create($key, $label, $type, $required, $suggestedValue = null) {
        $attribute = new Attribute;
        $attribute->key = $key;
        $attribute->label = $label;
        $attribute->type = $type;
        $attribute->required = $required;
        $attribute->suggestedValue = $suggestedValue;
        return $attribute;
    }

}