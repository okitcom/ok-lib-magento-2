<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;


use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;
use OK\Model\Amount;
use OK\Model\Cash\LineItem;
use OK\Model\Cash\Transaction;

/**
 * Class LineItemReducer. Compares lineitems from OK and applies discounts to magento products
 * @package Okitcom\OkLibMagento\Block\Middleware
 */
class LineItemReducer extends AbstractReducer
{

    function execute(Quote $quote, Transaction $response) {

        $quoteItems = $quote->getAllItems();

        $lineItems = $response->lineItems->all();
        foreach ($lineItems as $lineItem) {
            if (isset($lineItem->subItems) && $lineItem->subItems != null) {
                $quoteItem = $this->findQuoteProduct($lineItem->productCode, $quoteItems);

                if ($quoteItem != null) {
                    $discount = Amount::fromCents(0);
                    foreach ($lineItem->subItems->all() as $subItem) {
                        if (isset($subItem->type) && $subItem->type == "Coupon") {
                            // calculate discount
                            $discount = $discount->sub($subItem->totalAmount);
                        }
                    }

                    $quoteItem->setDiscountAmount($discount->getEuro());
                    $quoteItem->setBaseDiscountAmount($discount->getEuro());
                    $quoteItem->save();
                }



            }
        }
    }

    /**
     * Find a product by SKU
     * @param $sku string identifier
     * @param $items Quote\Item[] list of items
     * @return Quote\Item|null item
     */
    function findQuoteProduct($sku, $items) {
        foreach ($items as $item) {
            if ($item->getSku() === $sku) {
                return $item;
            }
        }
        return null;
    }

}