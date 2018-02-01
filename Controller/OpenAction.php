<?php
/**
 * Created by PhpStorm.
 * Date: 8/12/17
 */

namespace Okitcom\OkLibMagento\Controller;


use Magento\Framework\App\Action\Action;
use Magento\Framework\Exception\LocalizedException;
use OK\Credentials\Environment\DevelopmentEnvironment;
use OK\Credentials\Environment\Environment;
use OK\Credentials\Environment\ProductionEnvironment;
use OK\Credentials\OpenCredentials;
use OK\Service\Open;
use Okitcom\OkLibMagento\Helper\AuthorizationHelper;

abstract class OpenAction extends Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $session;

    /**
     * @var \Okitcom\OkLibMagento\Helper\ConfigHelper
     */
    protected $configHelper;

    /**
     * @var \Magento\Framework\Math\Random $mathRandom
     */
    protected $mathRandom;

    /**
     * @var AuthorizationHelper
     */
    protected $authorizationHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Model\Session $session
     * @param \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Okitcom\OkLibMagento\Helper\AuthorizationHelper $authorizationHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\Session $session,
        \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper,
        \Magento\Framework\Math\Random $mathRandom,
        \Okitcom\OkLibMagento\Helper\AuthorizationHelper $authorizationHelper
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->session = $session;
        $this->configHelper = $configHelper;
        $this->mathRandom = $mathRandom;
        $this->authorizationHelper = $authorizationHelper;
        parent::__construct($context);
    }

    protected function json(array $data, $responseCode = 200) {
        $result = $this->resultJsonFactory->create();
        $result->setData($data);
        $result->setHttpResponseCode($responseCode);
        return $result;
    }

    /**
     * @return \OK\Service\Open
     */
    protected function getOpenService() {
        return new Open(new OpenCredentials(null, $this->configHelper->getOpenConfig("open_secret"), $this->getEnvironment()));
    }

    /**
     * @return Environment
     */
    public function getEnvironment() {
        switch ($this->configHelper->getGeneralConfig("environment")) {
            case "development":
                return new DevelopmentEnvironment();
            case "production":
                return new ProductionEnvironment();
            default:
                throw new LocalizedException(__("Invalid OK environment"));
        }
    }

    protected function getOkLibEnvironment() {
        return $this->configHelper->getOkLibEnvironment();
    }

    protected function getLocale() {
        return $this->configHelper->getLocale();
    }

}