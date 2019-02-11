<?php
/**
 * Created by PhpStorm.
 * Date: 7/24/17
 */

namespace OK\Credentials\Environment;


class AlphaEnvironment extends Environment
{
    /**
     * Environment path part
     * @return string
     */
    function getBaseUrl() {
        return "works-api-alpha.okit.io";
    }
}