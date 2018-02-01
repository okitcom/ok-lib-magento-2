<?php
/**
 * Created by PhpStorm.
 * Date: 8/11/17
 */

namespace Okitcom\OkLibMagento\Controller\Callback;


use OK\Model\Network\Exception\NetworkException;
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
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Okitcom\OkLibMagento\Helper\AuthorizationHelper $authorizationHelper
     * @internal param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\Session $catalogSession,
        \Okitcom\OkLibMagento\Helper\ConfigHelper $configHelper,
        \Okitcom\OkLibMagento\Helper\CustomerHelper $customerHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Math\Random $mathRandom,
        \Okitcom\OkLibMagento\Helper\AuthorizationHelper $authorizationHelper
    ) {
        $this->customerHelper = $customerHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context, $resultJsonFactory, $catalogSession, $configHelper, $mathRandom, $authorizationHelper);
    }


    public function execute() {
        $externalId = $this->getRequest()->getParam("authorization");

        // clear guid session var when not in test mode
        if ($externalId != null) {
            $authorization = $this->authorizationHelper->getByExternalId($externalId);
            try {
                $response = $this->getOpenService()->get($authorization->getGuid());
            } catch (NetworkException $exception) {
                $this->messageManager->addErrorMessage(__(
                    "OK Open Status NOTFOUND"
                ));
                $redirect = $this->resultRedirectFactory->create();
                $redirect->setPath( 'customer/account/login');
                return $redirect;
            }

            if ($authorization != null && $response != null) {

                // Only sign in once
                $shouldSignin = $authorization->getState() != ConfigHelper::STATE_AUTHORIZATION_SUCCESS;
                $authorization->setState($response->state);
                $authorization->save();

                if ($shouldSignin && $response->state == ConfigHelper::STATE_AUTHORIZATION_SUCCESS) {
                    // log in user
                    $customer = $this->customerHelper->findOrCreate(
                        $response->token,
                        $response->attributes->get("name"),
                        $response->attributes->get("email"),
                        null
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
                $redirect = $this->resultRedirectFactory->create();
                $redirect->setPath( 'customer/account/login');
                return $redirect;
            }
        }

        $this->messageManager->addErrorMessage(__(
            "OK Open Status ERROR"
        ));
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath( 'customer/account/login');
        return $redirect;
    }

}