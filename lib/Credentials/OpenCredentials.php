<?php
/**
 * Created by PhpStorm.
 * Date: 7/21/17
 */

namespace OK\Credentials;

class OpenCredentials extends APICredentials
{

    /**
     * @return string
     */
    function path() {
        return "works/api/v2/open/authorisationrequests/";
    }
}