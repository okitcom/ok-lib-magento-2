<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Model;

class Campaign extends JSONObject {

    /**
     * @var int Campaign's id
     */
    public $id;
    /**
     * @var string Campaign's code
     */
    public $code;
    /**
     * @var int Campaign's account id
     */
    public $account;
    /**
     * @var int Id of the image used for campaign's background
     */
    public $backgroundImageId;

    /**
     * @var string Campaign's start date
     */
    public $campaignStart;
    /**
     * @var string Campaign's end date
     */
    public $campaignEnd;
    /**
     * @var string Campaign's conditions
     */
    public $conditions;
    /**
     * @var Campaign's description
     */
    public $description;
    /**
     * @var int Id of the logo image used for campaign
     */
    public $logoImageId;
    /**
     * @var string Merchant's name
     */
    public $merchant;
    /**
     * @var string Campaign's name
     */
    public $name;
    /**
     * @var Campaign's notes
     */
    public $notes;
    /**
     * @var string Campaign's state. Values: NEW, READY, ACTIVE, CANCELLED, CLOSED.
     */
    public $state;
    /**
     * @var string Campaign's type. Values: EVENT
     */
    public $type;
    /**
     * @var CampaignData Campaign's data details
     */
    public $data;

    // Override set so we construct a CampaignData object
    protected function set($data) {
        foreach ($data AS $key => $value) {
            if (is_array($value)) {
                $sub = new CampaignData();
                $sub->set($value);
                $value = $sub;
            }
            $this->{$key} = $value;
        }
    }

}
