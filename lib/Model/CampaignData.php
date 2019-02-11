<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Model;

class CampaignData extends JSONObject {

    /**
     * @var string Longitude and latitude separated by comma. Example: 24.234242, 25.45353.
     */
    public $location;
    /**
     * @var string Street where the event takes place.
     */
    public $venue;
    /**
     * @var int Logo image's id used by sponsor
     */
    public $sponsorImageId;

}