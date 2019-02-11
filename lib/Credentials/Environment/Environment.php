<?php

namespace OK\Credentials\Environment;

/**
 * Represents an environment of OK
 */
abstract class Environment
{

    /**
     * Environment base url.
     * @return string
     */
    abstract function getBaseUrl();
}