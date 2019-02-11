<?php
/**
 * Created by PhpStorm.
 * Date: 8/30/17
 */

namespace OK\Model;


class AuthorisationResult extends JSONObject
{

    /**
     * @var string Reference of the OK Authorisation Request
     */
    public $reference;

    /**
     * @var Amount Authorised amount
     */
    public $amount;

    /**
     * @var \DateTime Timestamp of authorisation
     */
    public $timestamp;

    /**
     * @var Location Location of authorisation.
     */
    public $location;

    /**
     * @var string Authorisation request result values. Possible values:
     *  *OK*
     *  *NotOKExpired*
     *  *NotOKDeclinedByUser*
     *  *NotOKCancelled*
     *  *OPEN
     *  *ERROR*
     */
    public $result;

    protected function child($key) {
        switch ($key) {
            case "location":
                return new Location;
            default:
                return parent::child($key);
        }
    }

    protected function customValue($key, $value) {
        switch ($key) {
            case "amount":
                return Amount::fromCents($value);
        }
        return parent::customValue($key, $value);
    }

    const RESULT_OK = "OK";
    const RESULT_NotOKExpired = "NotOKExpired";
    const RESULT_NotOKDeclinedByUser = "NotOKDeclinedByUser";
    const RESULT_NotOKCancelled = "NotOKCancelled";
    const RESULT_OPEN = "OPEN";
    const RESULT_ERROR = "ERROR";

}