<?php
/**
 * Created by PhpStorm.
 * Date: 8/17/17
 */

namespace Okitcom\OkLibMagento\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use OK\Credentials\CashCredentials;
use OK\Credentials\Environment\DevelopmentEnvironment;
use OK\Credentials\Environment\ProductionEnvironment;
use Okitcom\OkLibMagento\Model\Authorization;
use Okitcom\OkLibMagento\Model\Resource\Checkout\Collection;

class AuthorizationHelper extends AbstractHelper
{

    /**
     * @var \Okitcom\OkLibMagento\Model\Resource\Authorization\CollectionFactory $authorizationCollectionFactory
     */
    protected $authorizationCollectionFactory;

    /**
     * @var \Okitcom\OkLibMagento\Model\AuthorizationFactory
     */
    protected $authorizationFactory;

    /**
     * @var \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper
     */
    protected $configHelper;

    /**
     * Checkout constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Okitcom\OkLibMagento\Model\Resource\Authorization\CollectionFactory $authorizationCollectionFactory
     * @param \Okitcom\OkLibMagento\Model\AuthorizationFactory $authorizationFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Okitcom\OkLibMagento\Model\Resource\Authorization\CollectionFactory $authorizationCollectionFactory,
        \Okitcom\OkLibMagento\Model\AuthorizationFactory $authorizationFactory,
        \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper) {
        $this->authorizationCollectionFactory = $authorizationCollectionFactory;
        $this->authorizationFactory = $authorizationFactory;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    /**
     * @return Authorization
     */
    public function create() {
        return $this->authorizationFactory->create();
    }

    /**
     * Load authorization.
     * @param $guid
     * @return \Okitcom\OkLibMagento\Model\Authorization|null
     */
    public function getByGuid($guid) {
        $authorizations = $this->authorizationCollectionFactory->create()->addFieldToFilter("guid", $guid);
        return $authorizations->getFirstItem();
    }

    /**
     * Load authorization.
     * @param $externalId
     * @return \Okitcom\OkLibMagento\Model\Authorization|null
     */
    public function getByExternalId($externalId) {
        $authorizations = $this->authorizationCollectionFactory->create()->addFieldToFilter("external_id", $externalId);
        return $authorizations->getFirstItem();
    }

    public function getById($id) {
        return $this->authorizationFactory->create()->load($id);
    }

}