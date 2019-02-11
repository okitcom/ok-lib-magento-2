<?php

namespace OK;

/**
 * Trait SerializesAll. Defines functionality to serialize all fields of an object
 * @package OK
 */
trait SerializesAll
{

    protected function serializesNull() {
        return true;
    }

    function jsonSerialize() {

        $in = (array)$this;
        if (!$this->serializesNull()) {
            $out = (array)$this;
            foreach ($in as $key => $value) {
                if ($value === null) {
                // serialize proper
                    unset($out[$key]);
                }
            }
            return $out;
        }

//        // Fix DateTime conversion
//        foreach ($in as $key => $value) {
//            if ($value instanceof \JsonSerializable) {
//                // serialize proper
//                echo "Serializing diff " . $key;
//                $out[$key] = json_encode($value);
//            }
//        }
//
//        print_r($out);

        return $in;
    }
}