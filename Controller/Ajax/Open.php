<?php
/**
 * Created by PhpStorm.
 * Date: 8/11/17
 */

namespace Okitcom\OkLibMagento\Controller\Ajax;

use Magento\Checkout\Model\Session;
use OK\Credentials\CashCredentials;
use OK\Credentials\Environment\DevelopmentEnvironment;
use OK\Model\Amount;
use OK\Model\Attribute;
use OK\Model\Attributes;
use OK\Model\Cash\LineItem;
use OK\Model\Cash\TransactionRequest;
use OK\Model\Open\AuthorisationRequest;
use Okitcom\OkLibMagento\Controller\CheckoutAction;
use Okitcom\OkLibMagento\Controller\OpenAction;
use Okitcom\OkLibMagento\Helper\ConfigHelper;
use Okitcom\OkLibMagento\Setup\InstallData;

class Open extends OpenAction {

    public function execute() {
        if (ConfigHelper::TEST_MODE || $this->getRequest()->isAjax()) {

            // create object
            $ok = $this->getOpenService();

            $attributes = new Attributes();
            $attributes->name = Attribute::create("name", "Name", Attribute::TYPE_NAME, true);
            $attributes->email = Attribute::create("email", "Email", Attribute::TYPE_EMAIL, true);
            $attributes->address = Attribute::create("address", "Address", Attribute::TYPE_ADDRESS, false);
            $attributes->phone = Attribute::create("phone", "Phone", Attribute::TYPE_PHONENUMBER, false);

            $request = AuthorisationRequest::create("SignupLogin", "123", null);
            $request->attributes = $attributes;
            $request->permissions = ["TriggerPaymentInitiation"];

            $response = $ok->request($request);

            if (isset($response->guid)) {
                $this->session->setData(InstallData::OK_SESSION_TOKEN, $response->guid);

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