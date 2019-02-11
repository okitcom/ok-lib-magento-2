<?php
/**
 * Created by PhpStorm.
 * User: hidde
 * Date: 7/30/15
 * Time: 10:44 AM
 */

namespace OK\Model;

use OK\SerializesAll;

class TicketObject extends JSONObject implements \JsonSerializable {

    use SerializesAll;

    /**
     * @var int Ticket id
     */
    public $id;

    /**
     * @var string Ticket's identifier
     */
    public $guid;

    /**
     * @var string Account assigned to the ticket
     */
    public $issuedTo;

    /**
     * @var string State of the ticket. Values: USED, CLAIMED, UNCLAIMED, DELETED.
     */
    public $state;

    /**
     * @var int Id of the campaign the ticket is into.
     */
    public $campaign;
    /**
     * @var Campaign details
     */
    public $campaignDetails;
    /**
     * @var string Identifier of the TSP
     */
    public $externalId;
    /**
     * @var string Barcode assigned to the ticket
     */
    public $barcode;
    /**
     * @var string Date when the ticket was created
     */
    public $created;
    /**
     * @var string Date when the ticket was used
     */
    public $used;
    /**
     * @var string Data about the ticket
     */
    public $data;
    /**
     * @var string Type of the ticket's barcode representation. Values: QR, EAN13
     */
    public $barcodeType;

    /** @var  Event */
    public $event;

    // Override set so we construct a Campaign object
    protected function set($request) {
        $data = $request["ticket"];
        foreach ($data AS $key => $value) {
            if (is_array($value)) {
                $sub = new Campaign();
                $sub->set($value);
                $value = $sub;
            }
            $this->{$key} = $value;
        }
    }



//    /**
//     * Specify data which should be serialized to JSON
//     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
//     * @return mixed data which can be serialized by <b>json_encode</b>,
//     * which is a value of any type other than a resource.
//     * @since 5.4.0
//     */
//    function jsonSerialize() {
//        return [
//            "externalId" => $this->externalId,
//            "data" => $this->data,
//            "barcode" => $this->barcode,
//            "barcodeType" => $this->barcodeType,
//            "event" => json_encode($this->event)
//        ];
//    }
}
