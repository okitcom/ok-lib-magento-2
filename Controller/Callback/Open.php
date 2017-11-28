<?php
/**
 * Created by PhpStorm.
 * Date: 8/11/17
 */

namespace Okitcom\OkLibMagento\Controller\Callback;


use Okitcom\OkLibMagento\Controller\OpenAction;
use Okitcom\OkLibMagento\Helper\ConfigHelper;
use Okitcom\OkLibMagento\Helper\CustomerHelper;
use Okitcom\OkLibMagento\Setup\InstallData;

class Open extends OpenAction {

    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper
     * @param CustomerHelper $customerHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @internal param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\Session $catalogSession,
        \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper,
        \Okitcom\OkLibMagento\Helper\CustomerHelper $customerHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerHelper = $customerHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context, $resultJsonFactory, $catalogSession, $configHelper);
    }


    public function execute() {
        $guid = $this->getRequest()->getParam("okguid");

        // clear guid session var when not in test mode
        if ($guid != null && $this->session->getData(InstallData::OK_SESSION_TOKEN, !ConfigHelper::TEST_MODE) == $guid) {
            $response = $this->getOpenService()->get($guid);

            if (isset($response->authorisationResult)) {
                if ($response->authorisationResult->result == "OK") {
                    // log in user
                    $customer = $this->customerHelper->findOrCreate(
                        $response->token,
                        $response->attributes->get("name"),
                        $response->attributes->get("email"),
                        $response->attributes->get("address")
                    );

                    $this->customerSession->setCustomerDataAsLoggedIn($customer);
                    $this->customerSession->regenerateId();

                    $redirectUrl = $this->_redirect->getRedirectUrl();
                    if (!$redirectUrl) {
                        $redirectUrl = 'customer/account';
                    }

                    $redirect = $this->resultRedirectFactory->create();
                    $redirect->setPath($redirectUrl);
                    $this->messageManager->addSuccessMessage(__(
                        "OK Open Status OK"
                    ));
                    return $redirect;
                }

                $this->messageManager->addErrorMessage(__(
                    "OK Open Status " . $response->authorisationResult->result
                ));
            }
        }

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath( 'customer/account/login');
        return $redirect;
    }

}