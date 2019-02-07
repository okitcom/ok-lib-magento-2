<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;


use Magento\Quote\Model\Quote;
use OK\Model\Cash\Transaction;
use Okitcom\OkLibMagento\Helper\CustomerHelper;
use Okitcom\OkLibMagento\Setup\InstallData;

class CustomerReducer extends AbstractReducer
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Customer constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CustomerHelper $customerHelper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Okitcom\OkLibMagento\Helper\CustomerHelper $customerHelper,
                                \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
                                \Magento\Customer\Model\CustomerFactory $customerFactory) {
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerHelper = $customerHelper;
    }


    function execute(Quote $quote, Transaction $response) {
        $store = $this->storeManager->getStore();

        $searchResult = $this->customerHelper->findByToken($response->token);

        $customer = null;
        if ($searchResult == null) {
            // create new

            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($store->getWebsiteId());
            $potentialMatchOnEmail = $customer->loadByEmail($response->attributes->get("email")->value);
            if ($potentialMatchOnEmail->getEntityId() != null) {
                $customer = $this->customerRepository->getById($potentialMatchOnEmail->getEntityId());

                $customer->setCustomAttribute(InstallData::OK_TOKEN, $response->token);
                $this->customerRepository->save($customer);

                $quote->assignCustomer($customer);

            } else {
                $customer = $this->customerHelper->createCustomer(
                    $response->token,
                    $response->attributes->get("name"),
                    $response->attributes->get("email")
                );

                $quote->assignCustomer($customer);
            }
        } else {
            $quote->assignCustomer($searchResult);
        }

    }

}