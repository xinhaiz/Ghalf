<?php

namespace Ghalf\Config\Depend;

trait Instance {

    protected static $_instance = null;

    public function __construct() {
    }

    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
