<?php
/**
 * Created by PhpStorm.
 * Date: 7/21/17
 */

namespace OK\Builder;

class BuilderException extends \Exception
{
    public function __construct($message) {
        parent::__construct($message);
    }
}