<?php
/**
 * Created by PhpStorm.
 * Date: 8/27/17
 */

namespace Okitcom\OkLibMagento\Model\Quote;


class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Okitcom\OkLibMagento\Helper\CheckoutHelper
     */
    protected $_checkoutHelper;

    /**
     * Custom constructor.
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Okitcom\OkLibMagento\Helper\CheckoutHelper $checkout
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Okitcom\OkLibMagento\Helper\CheckoutHelper $checkout
    ) {
        $this->_priceCurrency = $priceCurrency;
        $this->_checkoutHelper = $checkout;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        //$address             = $shippingAssignment->getShipping()->getAddress();
        $label = 'OK';

        $checkout = $this->_checkoutHelper->getByQuote($quote->getId());
        if ($checkout != null && $checkout->getId() != null && $checkout->getGuid() != null) {
            $okresponse = $this->_checkoutHelper->getCashService()->get($checkout->getGuid());
            if ($okresponse != null && $okresponse->state == "ClosedAndCaptured" && $okresponse->authorisationResult->result == "OK") {
                $discountAmount = $okresponse->authorisationResult->amount->sub($okresponse->amount)->getEuro();

                $appliedCartDiscount = 0;
                if ($total->getDiscountDescription()) {
                    // If a discount exists in cart and another discount is applied, the add both discounts.
                    $appliedCartDiscount = $total->getDiscountAmount();
                    $discountAmount = $total->getDiscountAmount() + $discountAmount;
                    $label = $total->getDiscountDescription() . ', ' . $label;
                }

                $total->setDiscountDescription($label);
                $total->setDiscountAmount($discountAmount);
                $total->setBaseDiscountAmount($discountAmount);
                $total->setSubtotalWithDiscount($total->getSubtotal() + $discountAmount);
                $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $discountAmount);

                if (isset($appliedCartDiscount)) {
                    $total->addTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
                    $total->addBaseTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
                } else {
                    $total->addTotalAmount($this->getCode(), $discountAmount);
                    $total->addBaseTotalAmount($this->getCode(), $discountAmount);
                }

                return $this;
            }

        }

        return $this; // this was not an OK transaction
    }

}