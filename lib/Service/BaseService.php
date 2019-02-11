<?php
/**
 * Created by PhpStorm.
 * Date: 7/21/17
 */

namespace OK\Service;


use OK\Client;
use OK\Credentials\APICredentials;

class BaseService
{
    /** @var  Client */
    protected $client;

    /**
     * BaseService constructor.
     * @param APICredentials $credentials
     */
    public function __construct(APICredentials $credentials) {
        $this->client = new Client($credentials);
    }

    /**
     * Returns the value of attribute with name
     *
     * @param $attributes array of attributes
     * @param $name string Attribute's name
     * @return mixed Attribute value
     */
    public function getAttribute($attributes, $name) {
        foreach($attributes as $a) {
            if ($a->key == $name) {
                return $a->value;
            }
        }
        return null;
    }

    /**
     * Formats an array OK style. When object is single, format as object.
     * @param $array
     * @param $singleKey
     * @return array
     */
    protected function formatArray($array, $singleKey) {
        if (is_array($array) && count($array) == 1) {
            return [
                $singleKey => $array[0]
            ];
        }
        return $array;
    }


}