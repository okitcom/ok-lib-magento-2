<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Model;


use OK\SerializesAll;

class Event extends JSONObject implements \JsonSerializable
{
    use SerializesAll;

    /**
     * @var string Identifier for the event, set by the TSP. The externalId must
    uniquely identify the set of data that constitutes an event in
    the OK data model. When a second ticket is created with
    an event with an identical externalId but with different
    event data the event data from the original ticket will be
    overwritten. */
    public $externalId;
    /** @var string Name of the event */
    public $name;
    /** @var  string Description of the event */
    public $description;
    /** @var  string Conditions for the event */
    public $conditions;
    /** @var  \DateTime Start of the event */
    public $start;
    /** @var  \DateTime string End of the event */
    public $end;
    /** @var  string String Location of the event. Format: {lat},{lon} */
    public $location;
    /** @var  string Name of the venue of the event */
    public $venue;
    /** @var   */
    //public $sponsorImageId;

}