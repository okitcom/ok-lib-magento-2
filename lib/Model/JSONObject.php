<?php
/**
 * Created by PhpStorm.
 * Date: 7/6/17
 */

namespace OK\Model;

class JSONObject {

    public function __construct($json = false) {
        if ($json) $this->set(json_decode($json, true));
    }

    protected function set($data) {
        if ($this->isList() && !$this->isMultiple($data)) {
            $sub = $this->child(0);
            $sub->set($data);
            $value = $sub;
            $this->{$this->customKey(0, $value)} = $value;
            return;
        }
        foreach ($data AS $key => $rawValue) {
            $value = $this->customValue($key, $rawValue);
            if (is_array($value)) {
                $sub = $this->child($key);
                $sub->set($value);
                $value = $sub;
            }
//            if (is_string($value) && preg_match("/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z/", $value)) {
//                // convert to DateTime
//                //echo $key . " " . print_r($value, true) . " " . preg_match("/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z/", $value) . "\n";
//
//                $value = JsonDateTime::createFromJsonFormat($value);
//            }
            $cc = $this->customValue($key, $value);
            if (!$cc) {
                continue;
            }
            $this->{$this->customKey($key, $value)} = $value;
        }
    }

    /**
     * Return a custom typed child object for a specific key.
     * @param $key string to recognize child object
     * @return JSONObject a custom type or JSONObject (default)
     */
    protected function child($key) {
        return new JSONObject;
    }

    /**
     * Return a custom value representation.
     * @param $key string key
     * @param $value mixed value
     * @return mixed
     */
    protected function customValue($key, $value) {
        return $value;
    }

    /**
     * Return a custom key.
     * @param $key string original key
     * @param $value mixed value
     * @return string new key
     */
    protected function customKey($key, $value) {
        return $key;
    }

    /**
     * Used to work with OK API behavior to return a list with a single item as a single object.
     * @return bool whether this object represents an item in a list.
     */
    protected function isList() {
        return false;
    }

    /**
     * OK returns lists as a single object in JSON when theres only one result. This can cause
     * deserialization issues.
     * @param $value
     * @return bool whether there are multiple items in this list
     */
    private function isMultiple($value) {
        return array_key_exists("0", $value);
    }

}