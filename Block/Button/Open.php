<?php
/**
 * Created by PhpStorm.
 * Date: 8/28/17
 */

namespace Okitcom\OkLibMagento\Block\Button;


use Magento\Customer\Model\Context;
use Magento\Framework\View\Element\Template;
use Okitcom\OkLibMagento\Helper\ConfigHelper;

class Open extends Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * Button constructor.
     * @param Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper
     * @param array $data
     */
    public function __construct(Template\Context $context,
                                \Magento\Customer\Model\Session $customerSession,
                                \Magento\Framework\App\Http\Context $httpContext,
                                \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper,
                                array $data = []) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

}