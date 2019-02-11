<?php
/**
 * Created by PhpStorm.
 * Date: 7/22/17
 */

namespace OK\Model\Cash;


use OK\Model\Amount;
use OK\Model\JSONObject;
use OK\SerializesAll;

class LineItem extends JSONObject implements \JsonSerializable
{

    use SerializesAll;

    /**
     * @var integer Number of units of this product
     */
    public $quantity;

    /**
     * @var string identifies the product. Either LineItem.description,
     * LineItem.currency, LineItem.vat and LineItem.amount or
     * LineItem.productCode must be provided
     */
    public $productCode;

    /**
     * @var string describes the product
     */
    public $description;

    /**
     * @var Amount Product price in cents
     */
    public $amount;

    /**
     * @var integer Product vat percentage
     */
    public $vat;

    /**
     * @var string Product price currency. Acceptable value: EUR
     */
    public $currency;

    /**
     * @var bool Indicates if the line item is allowed to be taken into account for discounts and loyalty. Default: false
     */
    public $excludedFromCampaigns;

    /**
     * LineItem creator.
     * @param int $quantity
     * @param string $productCode
     * @param string $description
     * @param Amount $amount
     * @param int $vat
     * @param string $currency
     * @return LineItem
     */
    public static function create($quantity, $productCode, $description, $amount, $vat, $currency = "EUR") {
        $item = new LineItem;
        $item->quantity = $quantity;
        $item->productCode = $productCode;
        $item->description = $description;
        $item->amount = $amount;
        $item->vat = $vat;
        $item->currency = $currency;
        return $item;
    }

    /**
     * @var integer Identifier of this line item. *response*
     */
    public $id;

    /**
     * @var integer Identifier of the transaction *response*
     */
    public $transaction;

    /**
     * @var Amount Line items price in cents;
     */
    public $totalAmount;

    /**
     * @var string Currency of the total amount
     */
    public $totalCurrency;

    /**
     * @var LineItems Sub items for this line items, discounts and awarded points/punches. Sub line items will also appear in the line items array.
     */
    public $subItems;

    /**
     * @var string Type of the line item.
     * Possible values:
     * **Coupon** The line item represents a coupon redemption
     * **Points** The line item represents collected loyalty points.
     */
    public $type;

    protected function customValue($key, $value) {
        switch ($key) {
            case "totalAmount":
            case "Amount":
                return Amount::fromCents($value);
            case "excludedFromCampaigns":
                return boolval($value);
        }
        return parent::customValue($key, $value);
    }

    protected function child($key) {
        switch ($key) {
            case "subItems":
                return new LineItems;
            default:
                return parent::child($key);
        }
    }

    protected function serializesNull() {
        return false;
    }


}