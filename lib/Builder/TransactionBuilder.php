<?php
/**
 * Created by PhpStorm.
 * Date: 9/15/17
 */

namespace OK\Builder;


use OK\Builder\Extensions\BuildsAttributes;
use OK\Builder\Extensions\BuildsLineItems;
use OK\Model\Amount;
use OK\Model\Attribute;
use OK\Model\Attributes;
use OK\Model\Cash\LineItems;
use OK\Model\Cash\Transaction;
use OK\Model\Location;

/**
 * Class TransactionRequestBuilder
 * @package OK\Builder
 *
 * @api
 * @method TransactionBuilder setAmount(Amount $amount)
 * @method TransactionBuilder setDescription(string $description)
 * @method TransactionBuilder setCurrency(string $currency)
 * @method TransactionBuilder setReference(string $reference)
 * @method TransactionBuilder setLocation(Location $location)
 * @method TransactionBuilder setLineItems(LineItems $lineItems)
 * @method TransactionBuilder setBarcode(string $barcode)
 * @method TransactionBuilder setInitiationToken(string $initiationToken)
 * @method TransactionBuilder setCheckoutId(string $checkoutId)
 * @method TransactionBuilder setCheckoutType(string $checkoutType)
 * @method TransactionBuilder setCampaignCodes(string $campaignCodes)
 * @method TransactionBuilder setType(string $type)
 * @method TransactionBuilder setPaymentTransactions($paymentTransactions)
 * @method TransactionBuilder setAttributes(Attributes $attributes)
 * @method TransactionBuilder setRedirectUrl(string $redirectUrl)
 * @method TransactionBuilder setPermissions(string $permissions)
 * @method TransactionBuilder setPurchaseId(string $purchaseId)
 *
 * @method Transaction build()
 */
class TransactionBuilder extends GenericBuilder
{
    use BuildsAttributes, BuildsLineItems;

    protected function getObject() {
        return new Transaction;
    }
}