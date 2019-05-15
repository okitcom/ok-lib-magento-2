<?php
/**
 * Created by PhpStorm.
 * Date: 8/15/17
 */

namespace Okitcom\OkLibMagento\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class ConfigHelper extends AbstractHelper
{
    protected $storeManager;
    protected $objectManager;

    const DOMAIN_VERIFICATION_ID = "site_verification_id";

    const XML_PATH_OKCHECKOUT = 'ok/checkout/';
    const XML_PATH_OKOPEN = 'ok/open/';
    const XML_PATH_OKGENERAL = 'ok/general/';
    const TEST_MODE = true;

    const DATE_DB_FORMAT = 'Y-m-d H:i:s';
    const DATE_PENDING_OFFSET = ' -1 day'; // One day in the past
    const PENDING_STATES = [
        "NewPendingTrigger", "NewPendingApproval"
    ];
    const STATE_CHECKOUT_SUCCESS = "ClosedAndCaptured";
    const STATE_AUTHORIZATION_SUCCESS = "Processed";

    const OK_BASE_URL = "okit.com";

    public function __construct(Context $context,
                                ObjectManagerInterface $objectManager,
                                StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager  = $storeManager;
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }


    public function getCheckoutConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_OKCHECKOUT . $code, $storeId);
    }

    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_OKGENERAL . $code, $storeId);
    }

    public function getOpenConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_OKOPEN . $code, $storeId);
    }

    public function getDomainVerificationId() {
        $identifier = $this->getGeneralConfig(ConfigHelper::DOMAIN_VERIFICATION_ID);
        if (strpos($identifier, "ok_") !== FALSE) {
            $identifier = substr($identifier, 3);
        }
        if (strpos($identifier, ".html") !== FALSE
            && strpos($identifier, ".html") == strlen($identifier) - 5) {
            $identifier = substr($identifier, 0, strlen($identifier) - 5);
        }
        return $identifier;
    }


    public function getOkLibEnvironment() {
        $env = $this->getGeneralConfig("environment");
        $map = [
            "production" => "secure",
//            "development" => "local",
            "development" => "beta",
            "alpha" => "alpha",
            "test" => "test"
        ];
        $default = "secure";
        if (!array_key_exists($env, $map)) {
            return $default;
        }
        return $map[$env];
    }

    public function getLocale() {
        $code = $this->storeManager->getStore()->getLocaleCode();

        if (strtolower(substr( $code, 0, 1 )) === "nl") {
            return 'nl-NL';
        }else {
            return 'en-GB';
        }
    }
}
