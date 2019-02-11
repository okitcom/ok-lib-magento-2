<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Service;

use OK\Model\OKTicket\TicketPushRequest;
use OK\Model\OKTicket\TicketPushResponse;

class OKTicket extends BaseService {

    /**
     * Push a ticket.
     * @param TicketPushRequest $req
     * @return TicketPushResponse JSON
     * @throws \OK\Model\Network\Exception\NetworkException
     */
    public function push(TicketPushRequest $req) {
        return new TicketPushResponse($this->client->post("push", $req));
    }

}