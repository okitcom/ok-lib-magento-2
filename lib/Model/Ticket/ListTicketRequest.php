<?php
/**
 * Created by PhpStorm.
 * Date: 8/8/17
 */

namespace OK\Model\Ticket;


use OK\Model\JSONObject;
use OK\SerializesAll;

class ListTicketRequest implements \JsonSerializable
{

    use SerializesAll;

    /**
     * @var integer|null Filter tickets by campaign ID.
     */
    public $campaign;
    /**
     * @var string|null Filter tickets by external ID.
     */
    public $externalId;
    /**
     * @var string|null Filter tickets by state(s). Allowed values: USED, CLAIMED, UNCLAIMED, DELETED
     */
    public $state;
    /**
     * @var string|null Filter tickets by guid
     */
    public $guid;
    /**
     * @var integer|null Offset to start the list from
     */
    public $offset;
}