<?php
/**
 * Created by PhpStorm.
 * Date: 8/29/17
 */

namespace Okitcom\OkLibMagento\Helper;


use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManager;
use OK\Model\Attribute;
use Okitcom\OkLibMagento\Setup\InstallData;

class CustomerHelper extends AbstractHelper {

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * CustomerHelper constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager) {
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }


    /**
     * Find a customer by OK Token
     * @param $token string identifier
     * @return CustomerInterface|null if found
     */
    public function findByToken($token) {
        $filter = $this->filterBuilder
            ->setField(InstallData::OK_TOKEN)
            ->setConditionType('eq')
            ->setValue($token)
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filter]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchList = $this->customerRepository->getList($searchCriteria);
        if ($searchList->getTotalCount() > 0) {
            return $searchList->getItems()[0];
        }
        return null;
    }

    /**
     * Find customer by email
     * @param $email string email address
     * @param $websiteId
     * @return CustomerInterface|null if found
     */
    public function findByEmail($email, $websiteId) {
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $potentialMatchOnEmail = $customer->loadByEmail($email);
        if ($potentialMatchOnEmail->getEntityId() != null) {
            return $this->customerRepository->getById($potentialMatchOnEmail->getEntityId());
        }
        return null;
    }

    /**
     * Create a new customer object, provided the email address is unique
     * @param $token string OK token
     * @param $name Attribute name
     * @param $email Attribute email with unique value
     * @return CustomerInterface newly created customer
     */
    public function createCustomer($token, $name, $email) {
        $nameParts = explode(";", $name->value);

        $store = $this->storeManager->getStore();
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($store->getWebsiteId());
        $customer
            ->setStore($store)
            ->setFirstname($nameParts[0])
            ->setLastname($nameParts[1])
            ->setEmail($email->value)
            ->setPassword($email->value);
        $customer->save();
        $customer->setCustomAttribute(InstallData::OK_TOKEN, $token);
        $customer->save();
        return $this->customerRepository->getById($customer->getEntityId());
    }

    /**
     * Find a customer or create if doesnt exist.
     * @param $token string
     * @param $name Attribute
     * @param $email Attribute
     * @param $address Attribute
     * @return CustomerInterface|null
     */
    public function findOrCreate($token, $name, $email, $address) {
        $customer = $this->findByToken($token);
        if ($customer == null) {
            $store = $this->storeManager->getStore();
            $customer = $this->findByEmail($email->value, $store->getWebsiteId());
        }
        if ($customer == null) {
            $customer = $this->createCustomer($token, $name, $email);
        }
        $customer->setCustomAttribute(InstallData::OK_TOKEN, $token);
        $this->customerRepository->save($customer);
        return $customer;
    }

}
