<?php
/**
 * Created by PhpStorm.
 * Date: 9/18/17
 */

namespace OK\Builder;


use OK\Model\Ticket\ListTicketRequest;

/**
 * Class ListTicketRequestBuilder
 * @package OK\Builder
 *
 * @api
 *
 * @method ListTicketRequestBuilder setCampaign(integer $campaign)
 * @method ListTicketRequestBuilder setExternalId(integer $externalId)
 * @method ListTicketRequestBuilder setState(string $state)
 * @method ListTicketRequestBuilder setGuid(string $guid)
 * @method ListTicketRequestBuilder setOffset(integer $offset)
 * @method ListTicketRequest build()
 */
class ListTicketRequestBuilder extends GenericBuilder
{
    protected function getObject() {
        return new ListTicketRequest();
    }
}