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
use Okitcom\OkLibMagento\Model\Resource\Checkout\Collection;

class CheckoutHelper extends AbstractHelper
{

    /**
     * @var \Okitcom\OkLibMagento\Model\Resource\Checkout\CollectionFactory $checkoutCollectionFactory
     */
    protected $checkoutCollectionFactory;

    /**
     * @var \Okitcom\OkLibMagento\Model\CheckoutFactory
     */
    protected $checkoutFactory;

    /**
     * @var \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper
     */
    protected $configHelper;

    /**
     * Checkout constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Okitcom\OkLibMagento\Model\Resource\Checkout\CollectionFactory $authorizationCollectionFactory
     * @param \Okitcom\OkLibMagento\Model\CheckoutFactory $authorizationFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Okitcom\OkLibMagento\Model\Resource\Checkout\CollectionFactory $authorizationCollectionFactory,
        \Okitcom\OkLibMagento\Model\CheckoutFactory $authorizationFactory,
        \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper) {
        $this->checkoutCollectionFactory = $authorizationCollectionFactory;
        $this->checkoutFactory = $authorizationFactory;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    /**
     * Load checkout.
     * @param $guid
     * @return \Okitcom\OkLibMagento\Model\Checkout|null
     */
    public function getByGuid($guid) {
        $checkouts = $this->checkoutCollectionFactory->create()->addFieldToFilter("guid", $guid);
        return $checkouts->getFirstItem();
    }

    /**
     * Load checkout.
     * @param $quoteId
     * @return \Okitcom\OkLibMagento\Model\Checkout|null
     */
    public function getByQuote($quoteId) {
        $checkouts = $this->checkoutCollectionFactory->create()->addFieldToFilter("quote_id", $quoteId);
        return $checkouts->getFirstItem();
    }

    /**
     * Load checkout that is finalized.
     * @param $quoteId
     * @return \Okitcom\OkLibMagento\Model\Checkout|null
     */
    public function getFinalByQuote($quoteId) {
        $checkouts = $this->checkoutCollectionFactory->create()
            ->addFieldToFilter("quote_id", $quoteId)
            ->addFieldToFilter("state", ConfigHelper::STATE_CHECKOUT_SUCCESS)
            ->addFieldToFilter("sales_order_id", array('null' => true));
        return $checkouts->getFirstItem();
    }

    /**
     * Load checkout.
     * @param $salesOrderId
     * @return \Okitcom\OkLibMagento\Model\Checkout|null
     */
    public function getByOrderId($salesOrderId) {
        $checkouts = $this->checkoutCollectionFactory->create()->addFieldToFilter("sales_order_id", $salesOrderId);
        return $checkouts->getFirstItem();
    }

    public function getByExternalId($externalId) {
        $checkouts = $this->checkoutCollectionFactory->create()->addFieldToFilter("external_id", $externalId);
        return $checkouts->getFirstItem();
    }

    public function getById($id) {
        return $this->checkoutFactory->create()->load($id);
    }

    /**
     * @return Collection|null
     */
    public function getAllPending() {
        $fromDate = date(ConfigHelper::DATE_DB_FORMAT, strtotime(ConfigHelper::DATE_PENDING_OFFSET));

        $checkouts = $this->checkoutCollectionFactory->create()
            ->addFieldToFilter("state", ConfigHelper::PENDING_STATES)
            ->addFieldToFilter("updated_at", [
                "from" => $fromDate
            ]);
        return $checkouts;
    }

    public function getWorksUrl(\Okitcom\OkLibMagento\Model\Checkout $order) {
        $env = $this->configHelper->getOkLibEnvironment();
        $transactionId = $order->getOkTransactionId();
        return "https://" . $env . "." . ConfigHelper::OK_BASE_URL . "/okworks/#/transactions/" . $transactionId;
    }

    /**
     * @return \OK\Service\Cash
     * @throws LocalizedException
     */
    public function getCashService() {
        $credentials = new CashCredentials("", $this->configHelper->getCheckoutConfig("cash_secret"), $this->getEnvironment());
        //$credentials = new CashCredentials("", "sk_fa183567-15c9-4802-8cb8-0352760d6016", new DevelopmentEnvironment());
        $ok = new \OK\Service\Cash($credentials);
        return $ok;
    }

    /**
     * @return DevelopmentEnvironment|ProductionEnvironment
     * @throws LocalizedException
     */
    public function getEnvironment() {
        switch ($this->configHelper->getGeneralConfig("environment")) {
            case "development":
                return new DevelopmentEnvironment();
            case "production":
                return new ProductionEnvironment();
            default:
                throw new LocalizedException(__("Invalid OK environment: " . $this->configHelper->getCheckoutConfig("environment")));
        }
    }


}