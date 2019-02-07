<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Okitcom\OkLibMagento\Controller\DomainValidation;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Okitcom\OkLibMagento\Helper\ConfigHelper;

/**
 * Processes request to OK verification file and returns content
 */
class Index extends Action
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @param Context $context
     * @param ConfigHelper $resultPageFactory
     */
    public function __construct(
        Context $context,
        ConfigHelper $resultPageFactory
    ) {
        $this->configHelper = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * Generates OK verification page
     *
     * @return
     */
    public function execute()
    {
        return $this->getResponse()->setBody(
            $this->configHelper->getDomainVerificationId()
        );
    }
}
