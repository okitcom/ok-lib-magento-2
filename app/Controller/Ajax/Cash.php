<?php
/**
 * Created by PhpStorm.
 * Date: 8/11/17
 */

namespace Okitcom\OkLibMagento\Controller\Ajax;

use Okitcom\OkLibMagento\Controller\CheckoutAction;
use Okitcom\OkLibMagento\Helper\ConfigHelper;

class Cash extends CheckoutAction {

    public function execute() {
        if (ConfigHelper::TEST_MODE || $this->getRequest()->isAjax()) {

            $result = $this->requestCash();
            if ($result != null) {
                return $this->json([
                    "guid" => $result["guid"],
                    "initiation" => $result["initiation"],
                    "culture" => $this->getLocale(),
                    "environment" => $this->getOkLibEnvironment()
                ]);
            }

            return $this->json([
                "error" => "Cart is empty"
            ], 400);
        }

        return $this->json([
            "error" => "Request not permitted"
        ], 400);
    }

}