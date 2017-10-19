<?php
/**
 * Created by PhpStorm.
 * Date: 8/28/17
 */

namespace Okitcom\OkLibMagento\Block\Adminhtml\View;


use Magento\Framework\View\Element\Template;
use Magento\Sales\Block\Adminhtml\Order\View\Tab\Info;
use Magento\Sales\Model\Order;

class Works extends Template
{

    /**
     * @var \Okitcom\OkLibMagento\Helper\CheckoutHelper
     */
    protected $_checkoutHelper;

    public function __construct(Template\Context $context,
                                \Okitcom\OkLibMagento\Helper\CheckoutHelper $checkout,
                                array $data = []) {
        $this->_checkoutHelper = $checkout;
        parent::__construct($context, $data);
    }

    /**
     * @return bool whether this order was paid using OK.
     */
    public function orderIsOK() {
        return $this->getOrderOK() != null;
    }

    /**
     * Get OK checkout for this order.
     * @return null|\Okitcom\OkLibMagento\Model\Checkout if any
     */
    public function getOrderOK() {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId != null) {
            $ok = $this->_checkoutHelper->getByOrderId($orderId);
            return $ok;
        }
        return null;
    }

    /**
     * Get the url of this transaction in OK Works.
     * @return string
     */
    public function getWorksUrl() {
        return $this->_checkoutHelper->getWorksUrl($this->getOrderOK());
    }

}