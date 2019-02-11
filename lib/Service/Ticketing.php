<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Service;

use OK\Client;
use OK\Model\Location;
use OK\Model\Ticket\ListTicketRequest;
use OK\Model\Ticket\TicketCheckIn;
use OK\Model\TicketObject;

class Ticketing extends BaseService {

    /**
     * Returns the ticket
     * @param $guid string Ticket's identifier
     * @return TicketObject object with ticket data
     * @throws \OK\Model\Network\Exception\NetworkException
     */
    public function get($guid) {
        return new TicketObject($this->client->get('tickets/' . $guid));
    }

    /**
     * Creates a ticket
     * @param TicketObject $ticket
     * @return TicketObject
     * @throws \OK\Model\Network\Exception\NetworkException
     */
    public function create(TicketObject $ticket) {
        $response = $this->client->post('tickets', array('ticket' => $ticket));
        return new TicketObject($response);
    }

    /**
     * Updates the ticket
     * @param TicketObject $ticket
     * @return mixed
     * @throws \OK\Model\Network\Exception\NetworkException
     */
    public function update(TicketObject $ticket) {
        return new TicketObject($this->client->post('tickets/' . $ticket->guid, array("ticket" => $ticket)));
    }

    /**
     * @param TicketObject $ticket
     * @param $token
     * @return TicketObject
     * @throws \OK\Model\Network\Exception\NetworkException
     */
    public function push(TicketObject $ticket, $token) {
        $url = "tickets/push";
        $ticket->issuedTo = $token;

        return new TicketObject($this->client->post($url, array('ticket' => $ticket)));
    }

    /**
     * Check a ticket barcode for an event.
     * @param $eventId integer event id
     * @param $barcode string barcode
     * @param $terminalId integer _optional_ terminal id
     * @param $location Location _optional_ location
     * @return TicketCheckIn result
     * @throws \OK\Model\Network\Exception\NetworkException
     */
    public function check($eventId, $barcode, $terminalId = null, Location $location = null) {
        return new TicketCheckIn($this->client->post('check', [
            'event' => $eventId,
            'barcode' => $barcode,
            'terminalId' => $terminalId,
            'location' => $location
        ]));
    }

    /**
     * Get the QR code of a ticket barcode.
     * @param $guid string identifier of ticket
     * @return mixed|string qrcode
     */
    public function qr($guid) {
        return $this->client->getImage('tickets/' . $guid . '/qr.png');
    }

    /**
     * Retrieve a list of tickets
     * @param ListTicketRequest $request
     * @return string result
     * @throws \OK\Model\Network\Exception\NetworkException
     */
    public function getList(ListTicketRequest $request) {
        return new TicketObject($this->client->get("tickets", $request)); // TODO: FORMAT as right object, list vs. single object
    }

}