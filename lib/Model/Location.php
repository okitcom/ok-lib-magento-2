<?php
/**
 * Created by PhpStorm.
 * Date: 7/28/17
 */

namespace OK\Model;


use OK\SerializesAll;

class Location extends JSONObject implements \JsonSerializable
{
    use SerializesAll;

    /**
     * @var double latitude
     */
    public $lat;

    /**
     * @var double longitude
     */
    public $lon;
}