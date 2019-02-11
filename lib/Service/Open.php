<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Service;

use OK\Model\Open\AuthorisationRequest;

class Open extends BaseService
{
    /**
     * Initiate a new authorisation request.
     * @param AuthorisationRequest $request
     * @return AuthorisationRequest object.
     * @throws \OK\Model\Network\Exception\NetworkException
     */
    public function request(AuthorisationRequest $request)
    {
        return new AuthorisationRequest($this->client->post('', $request));
    }

    /**
     * Get the status of an authorisation request.
     * @param $guid string authorisation request guid
     * @return AuthorisationRequest partially filled object
     * @throws \OK\Model\Network\Exception\NetworkException
     */
    public function status($guid) {
        $method = $guid . '/status';
        return new AuthorisationRequest($this->client->get($method));
    }

    /**
     * Get an authorisation request including its data.
     * @param $guid string authorisation request guid
     * @return AuthorisationRequest
     * @throws \OK\Model\Network\Exception\NetworkException
     */
    public function get($guid) {
        return new AuthorisationRequest($this->client->get($guid));
    }

    public function qr($guid) {
        return $this->client->getImage($guid . '/qr.png');
    }
}