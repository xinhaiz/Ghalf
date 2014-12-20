<?php

namespace Ghalf\Config\Parser;

use \Ghalf\Config\Buffer\Buffer as Buffer;
use \Ghalf\Config\Buffer\Nodes as Nodes;
use \Ghalf\Config\Control\Control as Ctrl;
use \Ghalf\Config\Parser\Builitin as Builitin;

class Operators {

    use \Ghalf\Config\Depend\Instance;

    private $prev_char = null;
    private $char      = null;
    private $prev_code = 0;
    private $code      = 0;
    private $_ctrl     = null;
    private $_buffer   = null;
    private $_nodes    = null;
    private $_builitin = null;

    public function __construct() {
        $this->_buffer   = Buffer::getInstance();
        $this->_nodes    = Nodes::getInstance();
        $this->_ctrl     = Ctrl::getInstance();
        $this->_builitin = Builitin::getInstance();
    }

    /**
     * 设置字符
     *
     * @param string $char
     */
    public function setChar($char) {
        $this->prev_char = $this->char;
        $this->char      = (string) $char;
    }

    /**
     * 设置标记识别码
     *
     * @param int $code
     */
    public function setCode($code) {
        $this->prev_code = $this->code;
        $this->code      = (int)$code;
    }

    /**
     * 空格 操作
     *
     * @return boolean
     */
    public function space_tab(){
        if ($this->get(Ctrl::SINGLE_QUOT) === false && $this->get(Ctrl::DOUBLE_QUOT) === false) {
            if($this->_ctrl->get(Ctrl::BUILITIN) === false){
                $this->builitin();
            }

            return true;
        }

        return false;
    }

    /**
     * . 操作
     *
     * @return boolean
     */
    public function dot() {
        if (preg_match('/^[a-z0-9]$/i', $this->prev_char)) {
            $this->_nodes->buildChild($this->_buffer->pull());

            return true;
        }

        return false;
    }

    /**
     * : 操作
     *
     * @return boolean
     */
    public function extend() {
        if ($this->get(Ctrl::BRACKET) === true) {
            $extendName = trim($this->_buffer->pull());
            $this->_nodes->buildMain($extendName);
            $this->set(Ctrl::EXTEND, 1);

            if($this->get(Ctrl::BUILITIN) === true){
                $this->builitin($extendName);
            }

            return true;
        }

        return false;
    }

    /**
     * 节点操作, left
     *
     * @return boolean
     */
    public function left_bracket() {
        $isPassed = false;

        if ($this->get(Ctrl::BRACKET) === false && $this->get(Ctrl::SEMICONLON) === false) {
            $prevChar = $this->prev_char;
            $prevCode = $this->prev_code;

            switch (true) {
                case $prevChar === null:
                case $prevCode === 0x0EA:
                    $this->set(Ctrl::BRACKET, 1);
                    $isPassed = true;
                    break;
                case $prevCode === 0x0FB && $this->get(Ctrl::ARRAY_ING) === true:
                    $isPassed = true;
                    break;
                case preg_match('/^[a-z0-9]$/i', $prevChar) && $this->get(Ctrl::ARRAY_ING) === false:
                    $this->_nodes->buildChild($this->_buffer->pull());
                    $this->set(Ctrl::ARRAY_ING, 1);
                    $isPassed = true;
                    break;
                default :
                    $this->syntaxError();
            }
        }

        return $isPassed;
    }

    /**
     * 节点操作, right
     *
     * @return boolean
     */
    public function right_bracket() {
        switch (true) {
            case $this->get(Ctrl::EXTEND):
                $this->_nodes->copy(trim($this->_buffer->pull()));
                $this->set(Ctrl::EXTEND, 0);
                return true;
            case $this->get(Ctrl::BRACKET) === true && $this->get(Ctrl::EXTEND) === false:
                $nodeName = $this->_buffer->pull();
                $this->_nodes->buildMain($nodeName);

                if($this->get(Ctrl::BUILITIN) === true){
                    $this->builitin($nodeName);
                }

                $this->set(Ctrl::BRACKET, 0);
                return true;
            case $this->get(Ctrl::ARRAY_ING) === true:
                $prevChar = $this->prev_char;
                $prevCode = $this->prev_code;

                switch (true) {
                    case $prevCode === 0x0FA:
                        $this->set(Ctrl::ARRAY_ING, 0);
                        break;
                    case is_numeric($prevChar):
                        break;
                    case $prevCode === 0x0FE:
                    case $prevCode === 0x0FF:
                        $bufferVal = $this->_buffer->pull(false);

                        if (empty(trim($bufferVal))) {
                            $this->syntaxError('[' . $prevChar . $bufferVal . $prevChar);
                        }

                        $this->_buffer->shift();
                        $this->_buffer->pop();
                        break;
                    default :
                        $this->syntaxError('[');
                        break;
                }

                $this->_nodes->buildChild($this->_buffer->pull());
                return true;
            default :
                break;
        }

        return false;
    }

    /**
     * 注释操作
     *
     * @return boolean
     */
    public function semicolon() {
        if ($this->get(Ctrl::SEMICONLON) === false) {
            if ($this->get(Ctrl::EQUAL) === true) {
                $this->_nodes->push($this->_buffer->pull());
            }

            $this->set(Ctrl::SEMICONLON, 1);

            return true;
        }

        return false;
    }

    /**
     * = 操作
     *
     * @return boolean
     */
    public function equal() {
        $buffer = trim($this->_buffer->pull());

        if (!empty($buffer)) {
            $this->_nodes->buildChild($buffer);
        }

        $this->set(Ctrl::EQUAL, 1);

        return true;
    }

    /**
     * " 操作
     *
     * @return boolean
     */
    public function double_quot() {
        if ($this->get(Ctrl::EQUAL) === true && $this->get(Ctrl::SINGLE_QUOT) === false) {
            $def = trim($this->_buffer->pull(false));

            if(defined($def)){
                $this->_buffer->rebuffer();
                $this->_buffer->push(constant($def));
            }
        }

        if ($this->get(Ctrl::SINGLE_QUOT) === false && $this->get(Ctrl::ARRAY_ING) === false) {
            $this->set(Ctrl::DOUBLE_QUOT, !$this->get(Ctrl::DOUBLE_QUOT));
            return true;
        }

        return false;
    }

    /**
     * ' 操作
     *
     * @return boolean
     */
    public function single_quot() {
        if ($this->get(Ctrl::DOUBLE_QUOT) === false && $this->get(Ctrl::ARRAY_ING) === false) {
            $this->set(Ctrl::SINGLE_QUOT, !$this->get(Ctrl::SINGLE_QUOT));
            return true;
        }

        return false;
    }

    /**
     * \n 操作
     *
     * @return boolean
     */
    public function newline() {
        if($this->get(Ctrl::SEMICONLON) === false){
            if($this->get(Ctrl::BUILITIN) === true){
                $this->builitin($this->_buffer->pull());
            }

            if ($this->get(Ctrl::EQUAL) === true) {
                $this->_nodes->push($this->_buffer->pull());
            }

            // 收尾, 清理一些数据，直接压入主节点下
            $last = $this->_buffer->pull();

            if (!empty($last)) {
                $this->_nodes->buildChild();
                $this->_nodes->push($last);
            }
        }

        // 确保缓冲区没有数据
        $this->_buffer->rebuffer();

        $this->_ctrl->reset();
        $this->_builitin->reset();
        $this->set(Ctrl::NEWLINE, 1);
        return true;
    }

    /**
     * 填充缓冲区内容
     */
    public function push() {
        $char = $this->char;

        if (strpos($char, '\'') !== false) {
            $char = str_replace("\'", "'", $char);
        } else if (strpos($char, '\\') !== false) {
            $char = str_replace("\\", "\\\\", $char);
        }

        $this->_buffer->push($char);
    }

    /**
     * 内置关键字
     */
    protected function builitin($params = null){
        $builitin = $this->_builitin;

        if(!empty($params)){
            $builitin->setParam($params);
        }

        $builitin->run();
    }

    /**
     * 解析错误
     *
     * @throws \Exception
     */
    protected function syntaxError($str = null) {
        $_buffer = $this->_buffer;
        throw new \Exception('syntax error, near "' . $_buffer->pullPrev(). (string)$str . $_buffer->pull(false) . '"');
    }

    /**
     * $this->_ctrl->get
     *
     * @param sting $name
     * @return boolean
     */
    protected function get($name){
        return $this->_ctrl->get($name);
    }

    /**
     * $this->_ctrl->set
     *
     * @param sting $name
     */
    protected function set($name, $value){
        $this->_ctrl->set($name, $value);
    }

}
