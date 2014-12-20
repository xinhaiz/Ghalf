<?php

namespace Ghalf\Config\Parser;

use \Ghalf\Config\Struct\Mark as Mark;
use \Ghalf\Config\Buffer\Nodes as Nodes;
use \Ghalf\Config\Buffer\Char as Char;
use \Ghalf\Config\Buffer\Buffer as Buffer;
use \Ghalf\Config\Parser\Operators as Operators;
use \Ghalf\Config\Parser\Builitin as Builitin;
use \Ghalf\Config\Control\Control as Ctrl;

class Ruler {
    use \Ghalf\Config\Depend\Instance;

    protected $_mark       = null;
    protected $_chars      = null;
    protected $_buffer     = null;
    protected $_operators  = null;
    protected $_nodes      = null;

    /**
     * 解析
     *
     * @param \TestCore\Config\Char $chars
     * @return array
     */
    public function run(Char $chars) {
        $this->_chars     = $chars;
        $this->_buffer    = Buffer::getInstance();
        $this->_operators = Operators::getInstance();
        $this->_mark      = Mark::getInstance();
        $this->_nodes     = Nodes::getInstance();
        $this->_ctrl      = Ctrl::getInstance();

        Builitin::getInstance()->setChars($chars);

        while ($chars->current() < $chars->size()) {
            $str = $this->more($chars->read());
            $this->mark($str);
        }

        $data = $this->pull();
        $this->reset();

        return $data;
    }

    /**
     * ini 数据
     *
     * @return array
     */
    public function pull() {
        return Nodes::getInstance()->pull();
    }

    /**
     * 读取更多字符规则
     *
     * @param string $str
     * @return string
     */
    protected function more($str) {
        $chars = $this->_chars;

        if ($str === '\\') {
            $str .= $chars->read();
        }

        return $str;
    }

    /**
     * Mark
     *
     * @param string $char
     * @return boolean
     */
    protected function mark($char) {
        $operator = $this->operator($char);

        if ($operator === false) {
            $this->_operators->push($char);
        }
    }

    /**
     * 标识规则
     *
     * @param string $char
     * @return boolean
     */
    protected function operator($char) {
        $pass = false;
        $mark = $this->_mark;
        $ctrl = $this->_ctrl;
        $code = $mark->getMark($char);

        if($ctrl->get(Ctrl::SEMICONLON) === true && $code !== 0x0EA){
            return false;
        }

        $func   = $mark->getFun($code);
        $driver = $this->_operators;

        $driver->setChar($char);
        $driver->setCode($code);

        if ($code > 0 && method_exists($driver, $func)) {

            $result = $ctrl->pause($code) === false ? $driver->{$func}() : false;

            $pass = (bool)$result;
        }

        return $pass;
    }

    /**
     * 前置处理规则
     *
     * @param string $contents
     * @return string
     */
    protected function prefix($contents) {
        $contents = preg_replace('/\r\n/', "\n", $contents);
        return preg_replace('/\n\n+/', "\n", $contents);
    }

    /**
     * 重置
     */
    protected function reset() {
        $this->_nodes->reset();
        $this->_buffer->reset();
        $this->_ctrl->reset();
        $this->_chars     = null;
        $this->_buffer    = null;
        $this->_operators = null;
        $this->_nodes     = null;
    }

}
