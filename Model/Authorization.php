<?php
/**
 * Created by PhpStorm.
 * Date: 8/12/17
 */

namespace Okitcom\OkLibMagento\Model;


use Magento\Framework\Model\AbstractModel;

/**
 * Class Authorization
 * @package Okitcom\OkLibMagento\Model
 *
 * @method setExternalId(string $externalId)
 * @method setGuid(string $guid)
 * @method setOkTransactionId(integer $id)
 * @method setState(string $state)
 *
 * @method getExternalId(): string
 * @method getGuid(): string
 * @method getOkTransactionId(): integer
 * @method getState(): string
 */
class Authorization extends AbstractModel
{

    /**
     * Checkout constructor.
     */
    public function _construct() {
        $this->_init('Okitcom\OkLibMagento\Model\Resource\Authorization');
    }
}