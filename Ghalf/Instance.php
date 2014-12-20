<?php

namespace Ghalf;

trait Instance {
    protected static $_instance = null;

    public function __construct() {}

    /**
     * 单例
     *
     * ＠return \Ghalf\Instance
     */
    public static function getInstance(){
        if(!self::$_instance instanceof self){
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}
