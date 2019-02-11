<?php
/**
 * Created by PhpStorm.
 * Date: 7/21/17
 */

namespace OK\Credentials;


use OK\Credentials\Environment\Environment;

abstract class APICredentials
{

    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var Environment
     */
    protected $environment;

    /**
     * APICredentials constructor.
     * @param string $publicKey
     * @param string $privateKey
     * @param Environment $environment
     */
    public function __construct($publicKey, $privateKey, $environment) {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    abstract function path();

    /**
     * @return string
     */
    public function getPrivateKey() {
        return $this->privateKey;
    }

    /**
     * @return string
     */
    public function getPublicKey() {
        return $this->publicKey;
    }

    /**
     * @return Environment
     */
    public function getEnvironment() {
        return $this->environment;
    }

}