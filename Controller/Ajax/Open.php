<?php
/**
 * Created by PhpStorm.
 * Date: 8/11/17
 */

namespace Okitcom\OkLibMagento\Controller\Ajax;

use OK\Builder\AttributeBuilder;
use OK\Builder\AuthorisationRequestBuilder;
use OK\Model\Attribute;
use Okitcom\OkLibMagento\Controller\OpenAction;
use Okitcom\OkLibMagento\Helper\ConfigHelper;
use Okitcom\OkLibMagento\Setup\InstallData;

class Open extends OpenAction {

    public function execute() {
        if (ConfigHelper::TEST_MODE || $this->getRequest()->isAjax()) {

            $externalId = $this->mathRandom->getRandomString(24);

            $authorization = $this->authorizationHelper->create();
            $authorization->setExternalId($externalId);
            $authorization->save();

            $redirectUrl = $this->_url->getUrl("oklib/callback/open", [
                "_secure" => true,
                "authorization" => $externalId
            ]);

            // create object
            $ok = $this->getOpenService();

            $authorisationRequest = (new AuthorisationRequestBuilder())
                ->setPermissions("TriggerPaymentInitiation")
                ->setAction("SignupLogin")
                ->setRedirectUrl($redirectUrl)
                ->setReference("Online")
                ->addAttribute(
                    (new AttributeBuilder())
                        ->setKey("name")
                        ->setLabel("Name")
                        ->setType(Attribute::TYPE_NAME)
                        ->setRequired(true)
                        ->build()
                )
                ->addAttribute(
                    (new AttributeBuilder())
                        ->setKey("email")
                        ->setLabel("Email")
                        ->setType(Attribute::TYPE_EMAIL)
                        ->setRequired(true)
                        //->setVerified(true)
                        ->build()
                )
                ->addAttribute(
                    (new AttributeBuilder())
                        ->setKey("phone")
                        ->setLabel("Phone")
                        ->setType(Attribute::TYPE_PHONENUMBER)
                        ->setRequired(false)
                        ->build()
                )->build();

            $response = $ok->request($authorisationRequest);

            $authorization->setGuid($response->guid);
            $authorization->setOkTransactionId($response->id);
            $authorization->setState($response->state);
            $authorization->save();

            if (isset($response->guid)) {
                return $this->json([
                    "guid" => $response->guid,
                    "culture" => $this->getLocale(),
                    "environment" => $this->getOkLibEnvironment()
                ]);
            }

            return $this->json([
                "error" => "OK request failed"
            ], 400);
        }
        return $this->json([
            "error" => "Request not permitted"
        ], 400);
    }

}