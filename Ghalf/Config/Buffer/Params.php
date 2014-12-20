<?php

namespace Ghalf\Config\Buffer;

final class Params {

    use \Ghalf\Config\Depend\Instance;

    private $_prams = [];

    /**
     * 收集参数
     *
     * @param string $param
     */
    public function set($param){
        $this->_prams[] = $param;
    }

    /**
     * 拉出参数
     *
     * @return array
     */
    public function get($reset = true){
        $params = $this->_prams;

        if($reset === true){
            $this->reset();
        }

        return $params;
    }

    /**
     * 重置
     */
    public function reset(){
        $this->_prams = [];
    }

}
