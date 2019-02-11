<?php
/**
 * Created by PhpStorm.
 * Date: 7/10/17
 */

namespace OK\Model\OKTicket;


use OK\Model\TicketObject;
use OK\SerializesAll;

class TicketPushRequest implements \JsonSerializable
{
    use SerializesAll;

    /** @var  string token */
    public $token;

    /** @var  TicketObject[] */
    public $tickets;

}