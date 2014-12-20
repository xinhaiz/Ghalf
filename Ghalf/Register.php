<?php

namespace Ghalf;

final class Register {

    private static $_register = array();

    private function __construct() {}
    private function __sleep() {}
    private function __clone() {}
    private function __wakeup() {}

    /**
     * 注册
     *
     * @param string $mixed
     * @param any $maxed
     */
    public static function set($mixed, $maxed){
        self::$_register[$mixed] = $maxed;
    }

    /**
     * Get
     *
     * @param string $name
     * @return any
     */
    public static function get($name){
        return isset(self::$_register[$name]) ? self::$_register[$name] : null;
    }

    /**
     * 是否存在$name注册表
     *
     * @param string $name
     * @return boolean
     */
    public static function has($name){
        return (bool)(isset(self::$_register[$name]));
    }

    /**
     * 删除$name注册表
     *
     * @param　string $name
     */
    public static function del($name){
        if(self::has($name)){
            unset(self::$_register[$name]);
        }
    }
}