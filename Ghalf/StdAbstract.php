<?php

namespace Ghalf;

abstract class StdAbstract {

    final public function __construct($options = null) {
        if($options instanceof \Traversable || (is_array($options) && !empty($options))){
            foreach ($options as $key => $value){
                if(is_array($value)){
                    foreach ($value as $k => $v){
                        $this->set($key . '_'. $k, $v);
                    }
                } else {
                    $this->set($key, $value);
                }
            }
        }
    }

    /**
     * set : setKey(value)
     *
     * @param string $key
     * @param string $value
     */
    final public function __set($name, $value) {
        $this->set($name, $value);
    }

    /**
     * getName()
     *
     * @param string $name
     * @return string
     */
    final public function __get($name) {
        $fun = sprintf('get%s', ucfirst(str_replace('.', '_', $name)));

        return (method_exists($this, $fun)) ? call_user_func(array($this, $fun)) : null;
    }

    /**
     * set ; setKey(value)
     *
     * @param string $key
     * @param string $value
     */
    final private function set($key, $value){
        $fun = sprintf('set%s', ucfirst(str_replace('.', '_', $key)));

        if(method_exists($this, $fun)){
            call_user_func_array(array($this, $fun), array($value));
        }
    }

    abstract public function toArray();
}
