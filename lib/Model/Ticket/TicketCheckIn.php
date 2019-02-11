<?php
/**
 * Created by PhpStorm.
 * Date: 8/8/17
 */

namespace OK\Model\Ticket;


use OK\Model\Network\ApiResult;
use OK\Model\TicketObject;

class TicketCheckIn extends ApiResult {

    /**
     * @var TicketObject the ticket.
     */
    protected $ticket;

    protected function child($key) {
        switch ($key) {
            case "ticket":
                return new TicketObject;
            default:
                return parent::child($key);
        }
    }

    /**
     * Ticket was checked successfully.
     */
    const OK = "OK";
    /**
     * Input details are not valid: barcode or event.
     */
    const InvalidTicketCheckInDetails = "InvalidTicketCheckInDetails";
    /**
     * Ticket could not be identified based on the barcode.
     */
    const TicketUnavailable = "TicketUnavailable";
    /**
     * Ticket's state is not CLAIMED, so it cannot be used.
     */
    const TicketCannotBeUsed = "TicketCannotBeUsed";
    /**
     * Ticket is not registered to the provided event.
     */
    const TicketIsIncompatibleWithEvent = "TicketIsIncompatibleWithEvent";
    /**
     * Error occurred when marking ticket as used.
     */
    const UnableToUseTicket = "UnableToUseTicket";

}