<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Model\Cash;


use OK\Model\Amount;
use OK\Model\AuthorisationResult;
use OK\Model\JSONObject;
use OK\Model\Attributes;

class Transaction extends JSONObject
{

    public $id;
    /**
     * @var string
     */
    public $guid;
    /**
     * @var int
     */
    public $account;
    /**
     * @var string
     */
    public $state;
    /**
     * @var \DateTime
     */
    public $timestamp;

    /** @var  Amount in cents */
    public $amount;

    /** @var  string optional */
    public $currency;

    /**
     * @var string Description of the transaction to be displayed to the user
     */
    public $description;

    /**
     * @var string Unique merchant reference of the transaction
     */
    public $reference;

    /**
     * @var string Location of the purchase
     */
    public $location;

    /**
     * @var LineItems Array of line items in the transaction
     */
    public $lineItems;

    /**
     * @var string
     */
    public $barcode;

    /**
     * @var string Token previously obtained an authorised to initiate requests
     */
    public $initiationToken;

    /**
     * @var string Identifier of the POS
     */
    public $checkoutId;

    /**
     * @var string Type identifier of the POS. For example: ONLINE, INSTORE
     */
    public $checkoutType;

    /**
     * @var string Unique merchant reference of the purchase, displayed to the consumer
     */
    public $purchaseId;

    /**
     * @var string Space seperated list of applicable campaign
     * GUIDs. These campaigns are used in addition
     * to the campaigns OK determined applicable
     * based on the list of line items.
     */
    public $campaignCodes;

    /**
     * @var string Type of the transaction.
     * Default value: OKNOW
     * Allowed values: OKNOW , OKLATER , MANDATE
     */
    public $type;

    /**
     * @var mixed Array of pre-processed payment transactions in the transaction
     */
    public $paymentTransactions;

    /**
     * @var Attributes Array of attributes to request with the OK
     */
    public $attributes;

    /**
     * @var string URL to redirect to after processing finished
     */
    public $redirectUrl;

    /**
     * @var string[] Possible values: TriggerPaymentInitiation
     */
    public $permissions;

    /**
     * @var AuthorisationResult
     */
    public $authorisationResult;

    protected function child($key) {
        switch ($key) {
            case "attributes":
                return new Attributes;
            case "authorisationResult":
                return new AuthorisationResult;
            case "lineItems":
                return new LineItems;
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