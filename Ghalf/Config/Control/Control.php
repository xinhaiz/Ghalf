<?php

namespace Ghalf\Config\Control;

final class Control {

    use \Ghalf\Config\Depend\Instance;

    const BRACKET     = 'om_bracket';
    const ARRAY_ING   = 'om_array';
    const SEMICONLON  = 'om_semicolon';
    const EQUAL       = 'om_equal';
    const DOUBLE_QUOT = 'om_double_quot';
    const SINGLE_QUOT = 'om_single_quot';
    const NEWLINE     = 'om_newline';
    const EXTEND      = 'om_extend';
    const BUILITIN    = 'om_builitin';

    // om_* = true 打开标记 false 关闭标记
    private $om_bracket     = false; // [any] (node)
    private $om_array       = false; // [1][] (array)
    private $om_semicolon   = false; // ; any \n
    private $om_equal       = false; // \n any = any \n
    private $om_double_quot = false; // "any"
    private $om_single_quot = false; // 'any'
    private $om_newline     = false; // any \n any
    private $om_extend      = false; // [any:any]
    private $om_builitin    = false; // any(\t|\r)any*

    private function __clone() {}

    /**
     * 暂停解析器
     * 处理在部分条件下暂停解析
     * 开启时 "和' 内的所有字符当字符串处理
     * "和' 包含自己时，需前置 \
     *
     * @return boolean
     */
    public function pause($code = 0) {
        $pause  = false;
        $single = $this->get(self::SINGLE_QUOT);
        $double = $this->get(self::DOUBLE_QUOT);

        if(!in_array($code, array(0x0FE, 0x0FF))){
            $pause = (bool)($single === true || $double === true);
        }

        return $pause;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function get($name) {
        return ((isset($this->$name) && $this->$name === true) ? true : false);
    }

    /**
     * @param string $name
     * @param int|boolean $value
     * @return \Ini\Control\Control
     */
    public function set($name, $value){
        if (isset($this->$name)) {
            $this->$name = (bool)$value;
        }

        return $this;
    }

    public function reset() {
        $this->om_bracket     = false;
        $this->om_array       = false;
        $this->om_semicolon   = false;
        $this->om_equal       = false;
        $this->om_double_quot = false;
        $this->om_single_quot = false;
        $this->om_newline     = false;
        $this->om_extend      = false;
        $this->om_builitin    = false;
    }

}
