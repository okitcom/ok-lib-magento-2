<?php
/**
 * Created by PhpStorm.
 * Date: 7/7/17
 */

namespace OK\Model\Cash;
use OK\Model\Attribute;
use OK\Model\JSONObject;

/**
 * Class Attributes. Attributes can be accessed by class property on the key.
 * @package OK\Model
 * @getter
 */
class LineItems extends JSONObject implements \JsonSerializable
{

    /** @var  LineItem[] */
    protected $items = [];

    /**
     * @return LineItem[]
     */
    public function all() {
        return $this->items;
    }

    protected function child($key) {
        if (is_int($key)) {
            return new LineItem;
        }
        return new JSONObject;
    }

    protected function set($data) {
        $this->items = [];

        if (array_key_exists("0", $data)) {
            // multiple
            foreach ($data AS $key => $rawValue) {
                $item = new LineItem;
                $item->set($rawValue);
                $this->items[] = $item;
            }
        } else {
            // single
            $item = new LineItem;
            $item->set($data);
            $this->items[] = $item;
        }
    }

    public function add(LineItem $item) {
        $this->items[] = $item;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize() {
        return array_values($this->items); // strip keys from all vars
    }

    /**
     * Helper function to initialize collection of LineItem
     * @param LineItem[] $lineItems
     * @return LineItems
     */
    public static function init(array $lineItems) {
        $obj = new LineItems;
        $obj->items = $lineItems;
        return $obj;
    }
}