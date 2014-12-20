<?php

namespace Ghalf\Config\Parser;
use \Ghalf\Config\Struct\Builitin as BuilitinStruct;
use \Ghalf\Config\Buffer\Buffer as Buffer;
use \Ghalf\Config\Buffer\Nodes as Nodes;
use \Ghalf\Config\Buffer\Char as Char;
use \Ghalf\Config\Buffer\Params as Params;
use \Ghalf\Config\Control\Control as Ctrl;
use \Ghalf\Config\Depend\File as File;

class Builitin {
    use \Ghalf\Config\Depend\Instance;

    protected $_buffer = null;
    protected $_nodes  = null;
    protected $_struct = null;
    protected $_func   = null;
    protected $_ctrl   = null;
    protected $_chars  = null;
    protected $_params = null;

    public function __construct() {
        $this->_buffer = Buffer::getInstance();
        $this->_struct = BuilitinStruct::getInstance();
        $this->_ctrl   = Ctrl::getInstance();
        $this->_nodes  = Nodes::getInstance();
        $this->_params = Params::getInstance();
    }

    /**
     * 字符串操作对象
     *
     * @param Char $chars
     */
    public function setChars(Char $chars){
        $this->_chars = $chars;
    }

    public function run(){
        $builitin = $this->_ctrl->get(Ctrl::BUILITIN);
        $struct   = $this->_struct;

        if($this->_func === null && $builitin === false){
            $fun_name = (string)($this->_buffer->pull(false));

            if($struct->get($fun_name) > 0 && method_exists($this, '_' . $fun_name)){
                $this->_func = $fun_name;
                $this->_ctrl->set(Ctrl::BUILITIN, 1);
                $this->_buffer->rebuffer();

                return true;
            }
        }elseif($builitin === true){
            $func   = (string)$this->_func;
            $code   = $struct->get($func);

            if($code > 0){
                if($this->checkParam($code) === true){
                    $params = $this->_params;
                    $this->{'_' . $func}($params->get(false));
                    $this->_ctrl->set(Ctrl::BUILITIN, 0);
                    $params->reset();
                }else{
                    return false;
                }
            }
        }

        $this->reset();
    }

    /**
     * 重置
     */
    public function reset(){
        $this->_func = null;
    }

    /**
     * 添加参数
     *
     * @param string $params
     * @return \Ini\Parser\Builitin
     */
    public function setParam($params){
        $this->_params->set($params);
    }

    /**
     * 参数检查
     */
    public function checkParam($funCode){
        $struct = $this->_struct;
        $count  = $struct->getParam($funCode);
        $type   = $struct->getType($funCode);
        $args   = $this->_params->get(false);
        $curr   = strpos($type, '|');

        return (($curr === false) ? (bool)($count === count($args)) : (($curr + 1) === count($args)));
    }

////////////////////////// 关键字方法区域 BOF ////////////////////////
    /**
     * 内置关键字 import
     *
     * @param string $args
     */
    private function _import($args){
        if(!isset($args[0])){
            return false;
        }

        $str = File::getContent($args[0]);

        if(!empty($str)){
            $this->_chars->append($str . "\n"); // 增加 \n 避免意外, 类 \0
        }
    }

    /**
     * 内置关键字 final
     *
     * @param string $args
     */
    private function _final($args){
        $ctrl = $this->_ctrl;

        if(isset($args[0]) && $ctrl->get(Ctrl::BRACKET) === true && $ctrl->get(Ctrl::ARRAY_ING) === false){
            $this->_nodes->addFinal($args[0]);
        }
    }

////////////////////////// 关键字方法区域 EOF ////////////////////////
}
