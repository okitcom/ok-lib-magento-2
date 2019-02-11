<?php
/**
 * Created by PhpStorm.
 * Date: 8/9/17
 */

namespace OK\Model\Network;

use OK\Model\JSONObject;

/**
 * Class ApiResult. Represents a simple response from the API.
 * @package OK\Model\Network
 */
class ApiResult extends JSONObject {

    /**
     * @var string result of the API call.
     */
    protected $result;

    /**
     * @return bool whether this API response is a success response.
     */
    public function success() {
        return $this->result === ApiResult::OK;
    }

    /**
     * @var string represents a successful result.
     */
    const OK = "OK";

}