<?php
/**
 * Created by PhpStorm.
 * Date: 7/21/17
 */

namespace OK\Model\Network\Exception;


use Throwable;

class NetworkException extends \Exception
{
    public $result;

    public function __construct($res, $httpCode, Throwable $previous = null) {
        parent::__construct("OK API Error (" . $httpCode . "): " . $this->decodeError($res), $httpCode, $previous);
    }

    public function decodeError($res) {
        //return print_r($res, true);
        $response = json_decode($res);
        if ($response == null || !is_object($response)) {
            return $res;
        }
        if (isset($response->result)) {
            $this->result = $response->result;
            return $this->result;
        }
        if (isset($response->error)) {
            $this->result = $response->error;
            return $this->result . ": " . $response->message;
        }
        return null;
    }

}