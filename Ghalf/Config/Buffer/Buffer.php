<?php

namespace Ghalf\Config\Buffer;

final class Buffer {
    use \Ghalf\Config\Depend\Instance;

    /**
     * 数据缓冲
     *
     * @var array
     */
    protected $_buffer = [];

    /**
     * 上一级数据缓冲
     *
     * @var array
     */
    protected $_buffer_prev = [];

    /**
     * 数据缓冲
     *
     * @param string $str
     */
    public function push($str) {
        array_push($this->_buffer, $str);
    }

    /**
     * 弹出缓冲区数据
     *
     * @return string
     */
    public function pull($rebuffer = true) {
        $str = implode($this->_buffer);

        if ($rebuffer === true) {
            $this->rebuffer();
        }

        return $str;
    }

    /**
     * 弹出上级缓冲区数据
     *
     * @return string
     */
    public function pullPrev() {
        return implode($this->_buffer_prev);
    }

    /**
     * 将第一个入栈的数据弹出缓冲区
     */
    public function shift(){
        array_shift($this->_buffer);
    }

    /**
     * 将最后入栈的数据弹出缓冲区
     */
    public function pop(){
        array_pop($this->_buffer);
    }

    /**
     * 重置缓冲区
     */
    public function rebuffer() {
        $this->_buffer_prev = $this->_buffer;
        $this->_buffer      = [];
    }

    /**
     * 重置
     */
    public function reset() {
        $this->_buffer_prev = [];
        $this->_buffer      = [];
    }

}
