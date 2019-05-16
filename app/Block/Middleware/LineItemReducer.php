<?php
/**
 * Created by PhpStorm.
 * Date: 8/18/17
 */

namespace Okitcom\OkLibMagento\Block\Middleware;

use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;
use OK\Model\Amount;
use OK\Model\Cash\Transaction;
use Okitcom\OkLibMagento\Helper\DiscountHelper;

/**
 * Class LineItemReducer. Compares lineitems from OK and applies discounts to magento products
 * @package Okitcom\OkLibMagento\Block\Middleware
 */
class LineItemReducer extends AbstractReducer
{
    /**
     * @var DiscountHelper
     */
    private $discountHelper;

    /**
     * LineItemReducer constructor.
     *
     * @param DiscountHelper $discountHelper
     */
    public function __construct(DiscountHelper $discountHelper)
    {
        $this->discountHelper = $discountHelper;
    }

    function execute(Quote $quote, Transaction $transaction)
    {
        $discount = $this->discountHelper->getDiscountFromTransaction($transaction);
        if ($discount->getCents() === 0) {
            return $this;
        }

        $product = $this->discountHelper->getDiscountProduct($transaction, $discount);
        $config = new \Magento\Framework\DataObject();
        $config->setItem([
            'product' => $product->getId(),
            'qty' => 1,
            'price' => - $discount->getEuro(),
        ]);
        $quoteItem = $quote->addProduct($product, $config);
        $quoteItem->setDiscountAmount($discount->getEuro());
        $quoteItem->setBaseDiscountAmount($discount->getEuro());
        $quoteItem->save();

        return $this;
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
