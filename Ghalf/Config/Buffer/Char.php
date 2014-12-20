<?php

namespace Ghalf\Config\Buffer;

final class Char {

    private $_buffer   = null;
    private $_size     = 0;
    private $_position = 0;

    public function __construct($buffer) {
        $this->_buffer = $buffer;
        $this->_size   = strlen($buffer);
    }

    /**
     * 读取1长度字符
     *
     * @return string
     */
    public function read() {
        $str = $this->_buffer[$this->_position];
        $this->_position++;

        return $str;
    }

    /**
     * 读取指定长度字符
     *
     * @param int $length
     * @return string
     */
    public function readChar($length = 1) {
        $str = array();

        for ($i = 0; $i < $length; $i++) {
            array_push($str, $this->read());
        }

        return implode($str);
    }

    /**
     * 指针位置向前移动一个位置
     */
    public function next() {
        $this->_position++;
    }

    /**
     * 指针位置向后移动一个位置
     */
    public function prev() {
        $this->_position--;
    }

    /**
     * 总长度
     *
     * @return int
     */
    public function size() {
        return $this->_size;
    }

    /**
     * 当前指针位置
     *
     * @return int
     */
    public function current() {
        return $this->_position;
    }

    /**
     * 末尾附加新内容
     *
     * @param string $str
     */
    public function append($str){
        static $i = 1;

        if($i > 5){
            return false;
        }

        $this->_buffer .= (string)$str;
        $this->_size += strlen($str);

        $i++;

        return true;
    }

}
