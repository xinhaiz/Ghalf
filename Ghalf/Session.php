<?php

namespace Ghalf;

final class Session {

    use \Ghalf\Instance;

    const E = '.';

    protected $_sessionId = null;

    public function __construct() {
        if($this->_sessionId === null){
            session_start();
            $this->_sessionId = session_id();
        }
    }

    /**
     * 写
     *
     * @param string $name
     * @param any $value
     * @return \Ghalf\Session
     */
    public function set($name, $value){
        $name = explode(Session::E, $name);
        $key  = array_shift($name);

        if(!empty($name)){
            if(empty($_SESSION[$key]) || !is_array($_SESSION[$key])){
                $_SESSION[$key] = [];
            }

            $_SESSION[$key] = array_replace_recursive($_SESSION[$key], $this->_set($name, $value));
        }else{
            $_SESSION[$key] = $value;
        }

        return $this;
    }

    /**
     * 读
     *
     * @param string $name
     * @return any|null
     */
    public function get($name){
        $name = explode(Session::E, $name);
        $key  = array_shift($name);
        $set  = null;

        if(isset($_SESSION[$key])){
            $set = $_SESSION[$key];

            foreach ($name as $key){
                if(!isset($set[$key])){
                    $set = null;
                    break;
                }

                $set = $set[$key];
            }
        }

        return $set;
    }

    /**
     * 检测存在性
     *
     * @param type $name
     * @return boolean
     */
    public function has($name){
        $name = explode(Session::E, $name);
        $has  = false;
        $key  = array_shift($name);

        if(isset($_SESSION[$key])){
            $set = $_SESSION[$key];

            foreach ($name as $key){
                if(!isset($set[$key])){
                    $has = false;
                    break;
                }

                $has = true;
                $set = $set[$key];
            }
        }

        return $has;
    }

    /**
     * 删除
     *
     * @param string $name
     * @return boolean
     */
    public function del($name){
        if(strpos($name, self::E) === false){
            if(isset($_SESSION[$name])){
                unset($_SESSION[$name]);
            }
        }else{
            $this->set($name, null);
        }

        return true;
    }

    /**
     * 分段字符组成数组
     *
     * @param array $name
     * @param any $val
     * @param array $set
     * @return array
     */
    private function _set(array $name, $val, $set = []){
        $first = array_shift($name);

        if(!empty($name)){
            $set[$first] = $this->_set($name, $val, $set);
        }else{
            $set[$first] = $val;
        }

        return $set;
    }
}