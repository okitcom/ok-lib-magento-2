<?php
/**
 * Creator: Henny Krijnen (henny.krijnen@notive.nl) 2019/5/10 11:36:9
 * Copyright (c) 2019. Notive (https://notive.nl)
 */

namespace Okitcom\OkLibMagento\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Filesystem\DirectoryList;
use OK\Model\Amount;
use OK\Model\Cash\Transaction;

/**
 * Class DiscountHelper
 *
 * @package Okitcom\OkLibMagento\Helper
 */
class DiscountHelper
{
    const OK_VIRTUAL_DISCOUNT_SKU = 'OK_VIRTUAL_DISCOUNT';

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * DiscountHelper constructor.
     *
     * @param Product                                     $product
     * @param ProductFactory                              $productFactory
     * @param ProductRepositoryInterface                  $productRepository
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Product $product,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        DirectoryList $directoryList
    ) {
        $this->product = $product;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->directoryList = $directoryList;
    }

    /**
     * @param Transaction $transaction
     *
     * @return Amount
     */
    public function getDiscountFromTransaction(Transaction $transaction)
    {
        $discount = Amount::fromCents(0);
        $lineItems = $transaction->lineItems->all();
        foreach ($lineItems as $lineItem) {
            if (isset($lineItem->subItems) && $lineItem->subItems != null) {
                foreach ($lineItem->subItems->all() as $subItem) {
                    if (isset($subItem->type) && $subItem->type == "Coupon") {
                        $discount = $discount->sub($subItem->totalAmount);
                    }
                }
            }
        }

        return $discount;
    }

    /**
     * @return Interceptor
     */
    public function getDiscountProduct()
    {
        $productId = $this->product->getIdBySku(self::OK_VIRTUAL_DISCOUNT_SKU);
        if (!$productId) {
            $product = $this->createDiscountProduct();
            $productId = $this->product->getIdBySku(self::OK_VIRTUAL_DISCOUNT_SKU);
        }

        return $this->productFactory->create()->load($productId);
    }

    /**
     * @return Interceptor
     */
    private function createDiscountProduct()
    {
        $product = $this->productFactory->create();

        $product->setSku(self::OK_VIRTUAL_DISCOUNT_SKU);
        $product->setName('OK Discount');
        $product->setStatus(1);
        $product->setVisibility(1);
        $product->setTypeId(Type::TYPE_VIRTUAL);
        $product->setPrice(0);
        $product->setAttributeSetId(4);
        $product->setStockData([
            'use_config_manage_stock' => 0,
            'manage_stock' => 0,
            'min_sale_qty' => 1,
            'max_sale_qty' => 2,
            'is_in_stock' => 1,
        ]);

        $product->addImageToMediaGallery(
            $this->getDiscountImagePath(),
            [
                'image',
                'small_image',
                'thumbnail',
            ],
            false,
            false
        );

        return $product->save($product);
    }

    /**
     * Returns path containing image
     *
     * @return string
     */
    private function getDiscountImagePath()
    {
        $imageName = 'ok-payment.png';
        $originalImagePath = $this->directoryList->getPath('app'). '/code/Okitcom/OkLibMagento/view/frontend/web/images/'.$imageName;
        $newImagePath = $this->createOkDirectory().$imageName;

        if (!copy($originalImagePath, $newImagePath) || !file_exists($newImagePath)) {
            throw new \RuntimeException(sprintf('Image "%s" was not found', $newImagePath));
        }

        return $imageName;
    }

    /**
     * @return string
     */
    private function createOkDirectory(): string
    {
        $newDir = $this->directoryList->getPath('media') . '/ok/';
        if (!file_exists($newDir) && !mkdir($newDir, 0777, true) && !is_dir($newDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $newDir));
        }
        return $newDir;
    }
}
