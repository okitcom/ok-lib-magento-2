<?php
/**
 * Created by PhpStorm.
 * Date: 8/11/17
 */

namespace Okitcom\OkLibMagento\Controller\Ajax;

use Okitcom\OkLibMagento\Controller\CheckoutAction;
use Okitcom\OkLibMagento\Helper\ConfigHelper;

class Buynow extends CheckoutAction {

    public function execute() {
        if (ConfigHelper::TEST_MODE || $this->getRequest()->isAjax()) {

            $request = $this->getRequest();
            $product_id = (int)$request->getParam("product");

            if ($product_id == null) {
                return $this->json([
                    "error" => "Invalid product."
                ]);
            }

            $quote = $this->quoteHelper->createQuote([[
                'product_id' => $product_id,
                'request' => $request->getParams()
            ]]);

            $result = $this->requestCash($quote);
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