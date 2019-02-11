<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Model\Open;


use OK\Model\Amount;
use OK\Model\AuthorisationResult;
use OK\Model\JSONObject;
use OK\Model\Attributes;
use OK\Model\Location;
use OK\SerializesAll;

class AuthorisationRequest extends JSONObject implements \JsonSerializable
{

    use SerializesAll;

    /**
     * @var integer Identifier of the transaction.
     */
    public $id;

    /**
     * @var string Identifier of the authorisation request
     */
    public $guid;

    /**
     * @var integer identifier of the associated account
     */
    public $account;

    /**
     * @var integer identifier of the associated service
     */
    public $service;

    /**
     * @var \DateTime timestamp
     */
    public $timestamp;

    /**
     * @var string Authorisation request state
     */
    public $state;

    /**
     * @var string URL to redirect to after processing finished
     */
    public $redirectUrl;

    /**
     * @var string Description of the action to be displayed to the user
     */
    public $action;

    /**
     * @var string Unique merchant reference of the authorisation request
     */
    public $reference;

    /**
     * @var Location Location of the authorisation request
     */
    public $location;

    /**
     * @var string Token previously obtained to initiate requests
     */
    public $token;

    /**
     * @var string Phone number, previously obtained to initiate requests
     */
    public $phoneNumber;

    /**
     * @var string[] Possible values: TriggerPaymentInitiation
     */
    public $permissions;

    /**
     * @var string Time in seconds in which the authorisation request must be triggered
     */
    public $triggerPeriod;

    /**
     * @var Attributes
     */
    public $attributes;

    /**
     * @var Amount
     */
    public $amount;

    /**
     * @var AuthorisationResult
     */
    public $result;

    protected function child($key) {
        switch ($key) {
            case "attributes":
                return new Attributes;
            case "location":
                return new Location;
            case "authorisationResult":
                return new AuthorisationResult;
            default:
                return new JSONObject;
        }
    }

    protected function customValue($key, $value) {
        switch ($key) {
            case "amount":
                return Amount::fromCents($value);
        }
        return parent::customValue($key, $value);
    }


}