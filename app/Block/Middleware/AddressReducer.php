<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;


use Magento\Quote\Model\Quote;
use OK\Model\Attribute;
use OK\Model\Cash\Transaction;

class AddressReducer extends AbstractReducer
{

    function execute(Quote $quote, Transaction $response) {
        $this->mapOkAddress($quote->getShippingAddress(), $response);
        $this->mapOkAddress($quote->getBillingAddress(), $response);
    }

    private function mapOkAddress(Quote\Address $address, Transaction $response) {
        $nameParts = explode(";", $response->attributes->name->value);
        $okAddress = $response->attributes->address;

        $address->setFirstname($nameParts[0]);
        $address->setLastname($nameParts[1]);
        $address->setStreet($okAddress->addressComponent(Attribute::ADDRESS_STREET)
            . " "
            . $okAddress->addressComponent(Attribute::ADDRESS_NUMBER));
        $address->setPostcode($okAddress->addressComponent(Attribute::ADDRESS_ZIP));
        $address->setCity($okAddress->addressComponent(Attribute::ADDRESS_CITY));
        $address->setCountryId("NL"); // TODO: Change
        $address->setTelephone("31620789955");
        return $address;
    }
}