<?php
/**
 * Created by PhpStorm.
 * Date: 8/8/17
 */

namespace OK\Model;

/**
 * Class Amount. Manages all money amounts.
 * @package OK\Model
 */
class Amount implements \JsonSerializable {

    /**
     * @var integer value of the amount **in cents**
     */
    protected $value;

    /**
     * @param $cents int
     * @return Amount
     */
    public static function fromCents($cents) {
        $amount = new Amount();
        $amount->value = $cents;
        return $amount;
    }

    /**
     * @param $euros float
     * @return Amount
     */
    public static function fromEuro($euros) {
        $amount = new Amount();
        $amount->value = round($euros * 100.0);
        return $amount;
    }

    /**
     * Get the amount in cents
     * @return int
     */
    public function getCents() {
        return $this->value;
    }

    /**
     * Get the amount in euro.
     * @return float
     */
    public function getEuro() {
        return round($this->value / 100.0, 2);
    }

    /**
     * Add another amount to self
     * @param Amount $amount the amount to add
     * @return Amount result
     */
    public function add(Amount $amount) {
        return Amount::fromCents($amount->getCents() + $this->getCents());
    }

    /**
     * Substract another amount from self
     * @param Amount $amount the amount to subtract
     * @return Amount result
     */
    public function sub(Amount $amount) {
        return Amount::fromCents($this->getCents() - $amount->getCents());
    }

    /**
     * Multiply another amount with self
     * @param Amount $amount the amount to multiply
     * @return Amount result
     */
    public function multiply(Amount $amount) {
        return Amount::fromCents($amount->getCents() * $this->getCents());
    }

    /**
     * Divide another amount with self
     * @param Amount $amount the amount to divide with
     * @return Amount result
     */
    public function divide(Amount $amount) {
        return Amount::fromCents($this->getCents() / $amount->getCents());
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize() {
        return $this->value;
    }
}