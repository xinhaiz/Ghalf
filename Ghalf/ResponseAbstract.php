<?php

namespace Ghalf;

class ResponseAbstract {

    use \Ghalf\Instance;

    const Name = '__default__';

    protected $_body = [];
    protected $_header = [];

    private function __clone() {}

    public function __toString() {
        return $this->getBody();
    }

    /**
     * 设置名称
     *
     * @param string $name
     * @return string
     */
    protected function name($name){
        return $name ? $name : self::Name;
    }

    /**
     * 设置Body
     *
     * @param string $body
     * @param string $name
     * @return \Ghalf\ResponseAbstract
     */
    public function setBody($body, $name = null){
        $this->_body[$this->name($name)] = $body;

        return $this;
    }

    /**
     * 往已有的响应body前插入新的内容
     *
     * @param string $body
     * @param string $name
     * @return \Ghalf\ResponseAbstract
     */
    public function prepenBody($body, $name = null){
        $name  = $this->name($name);
        $pBody = (isset($this->_body[$name])) ? $this->_body[$name] : '';

        $this->_body[$name] = implode(array($pBody, $body));

        return $this;
    }

    /**
     * 往已有的响应body后插入新的内容
     *
     * @param string $body
     * @param string $name
     * @return \Ghalf\ResponseAbstract
     */
    public function appendBody($body, $name = null){
        $name  = $this->name($name);
        $aBody = (isset($this->_body[$name])) ? $this->_body[$name] : '';

        $this->_body[$name] = implode(array($body, $aBody));

        return $this;
    }

    /**
     * 清除已经设置的响应body
     *
     * @return \Ghalf\ResponseAbstract
     */
    public function clearBody(){
        $this->_body = [];

        return $this;
    }

    /**
     * 获取已经设置的响应body
     *
     * @return string
     */
    public function getBody(){
        return (!empty($this->_body)) ? implode($this->_body) : null;
    }

    /**
     * 发送响应给请求端
     */
    public function response(){
        echo $this->getBody();
    }
}
