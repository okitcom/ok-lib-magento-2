<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Okitcom\OkLibMagento\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Route\ConfigInterface;
use Magento\Framework\App\Router\ActionList;
use Magento\Framework\App\RouterInterface;
use Okitcom\OkLibMagento\Helper\ConfigHelper;

/**
 * Matches applicaton action in case an OK domain verification file was requested
 */
class DomainValidationRouter implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var ActionList
     */
    private $actionList;

    /**
     * @var ConfigInterface
     */
    private $routeConfig;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @param ActionFactory $actionFactory
     * @param ActionList $actionList
     * @param ConfigInterface $routeConfig
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionList $actionList,
        ConfigInterface $routeConfig,
        ConfigHelper $configHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->actionList = $actionList;
        $this->routeConfig = $routeConfig;
        $this->configHelper = $configHelper;
    }

    /**
     * Checks if robots.txt file was requested and returns instance of matched application action class
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $verification_identifier = $this->configHelper->getDomainVerificationId();
        if ($verification_identifier == null) {
            return null;
        }
        $identifier = trim($request->getPathInfo(), '/');
        if ($identifier !== "ok_" . $verification_identifier . ".html") {
            return null;
        }

        $modules = $this->routeConfig->getModulesByFrontName('oklib');
        if (empty($modules)) {
            return null;
        }

        $actionClassName = $this->actionList->get($modules[0], null, 'DomainValidation', 'index');
        $actionInstance = $this->actionFactory->create($actionClassName);
        return $actionInstance;
    }
}
