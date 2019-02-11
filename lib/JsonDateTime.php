<?php
/**
 * Created by PhpStorm.
 * Date: 7/11/17
 */

namespace OK;

use DateTimeZone;

class JsonDateTime extends \DateTime implements \JsonSerializable
{
    const FORMAT = "Y-m-d\TH:i:s\Z";

    public function jsonSerialize()
    {
        return $this->format(JsonDateTime::FORMAT);
    }

    public static function createFromDateTime(\DateTime $in) {
        return new JsonDateTime("@" . $in->getTimestamp(), $in->getTimezone());
    }

    public static function createFromJsonFormat($time, DateTimeZone $timezone = null) {
        parent::createFromFormat(JsonDateTime::FORMAT, $time, $timezone);
    }
}
