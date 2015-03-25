<?php

namespace Ghalf;

class RequestAbstract {

    protected $_controller   = null;
    protected $_action       = null;
    protected $_module       = null;
    protected $_params       = null;
    protected $_method       = null;
    protected $_base_uri     = null;
    protected $_request_uri  = '/';
    protected $_query_string = null;
    protected $_dispatched   = false;
    protected $_routed       = false;
    protected $_query        = [];
    protected $_post         = [];

    public function __construct() {
        $this->_method = $this->getServer('REQUEST_METHOD');
        $this->_query  = filter_input_array(\INPUT_GET, \FILTER_DEFAULT) ?: [];
        $this->_post   = filter_input_array(\INPUT_POST, \FILTER_DEFAULT) ?: [];
    }

    /**
     * 当前控制器名称
     *
     * @return string
     */
    public function getControllerName() {
        return $this->_controller;
    }

    /**
     * 当前模块名称
     *
     * @return string
     */
    public function getModuleName() {
        return $this->_module;
    }

    /**
     * 当前Action名称
     *
     * @return string
     */
    public function getActionName() {
        return $this->_action;
    }

    /**
     * 当前模块名称
     *
     * @return string
     */
    public function setModuleName($name) {
        $this->_module = ucfirst(strtolower((string)$name));

        return $this;
    }

    /**
     * 当前控制器名称
     *
     * @return string
     */
    public function setControllerName($name) {
        $this->_controller = ucfirst(strtolower((string) $name));

        return $this;
    }

    /**
     * 当前Action名称
     *
     * @return string
     */
    public function setActionName($name) {
        $this->_action = strtolower((string) $name);

        return $this;
    }

    /**
     * GET data / URL
     *
     * @param string $name
     * @param string $default
     * @return string|number
     */
    public function getParam($name, $default = null) {
        return isset($this->_params[$name]) ? $this->_params[$name] : $default;
    }

    /**
     * 获取所有参数 GET
     *
     * @return string
     */
    public function getParams() {
        return $this->_params;
    }

    /**
     * 路由参数
     *
     * @param string $name
     * @param string $value
     * @return \Ghalf\RequestAbstract
     */
    public function setParam($name, $value) {
        $this->_params[$name] = $value;

        return $this;
    }

    /**
     * 获取当前请求的类型,
     * 可能的返回值为GET,POST,HEAD,PUT,CLI
     *
     * @return string
     */
    public function getMethod() {
        return $this->_method;
    }

    /**
     * Request URI
     *
     * @return string
     */
    public function getRequestUri() {
        return $this->_request_uri;
    }

    /**
     * Request URI
     *
     * @return string
     */
    public function setRequestUri($uri) {
        $this->_request_uri = (string) $uri;

        return $this;
    }

    /**
     * Base URI
     *
     * @return string
     */
    public function getBaseUri() {
        return $this->_base_uri;
    }

    /**
     * Base URI
     *
     * @return string
     */
    public function setBaseUri($uri) {
        $this->_base_uri = $uri;

        return $this;
    }

    /**
     * 是否已完成分发
     *
     * @return boolean
     */
    public function isDispatched() {
        return (bool) $this->_dispatched;
    }

    /**
     * 设置分发状态
     *
     * @param boolean $switch
     * @return \Ghalf\RequestAbstract
     */
    public function setDispatched($switch) {
        $this->_dispatched = (bool) $switch;

        return $this;
    }

    /**
     * 捕获当前发生的异常对象
     *
     */
    public function getException() {

    }

    /**
     * 是否已完成路由解析
     *
     * @return boolean
     */
    public function isRouted() {
        return (bool) $this->_routed;
    }

    /**
     * 设置路由解析状态
     *
     * @param boolean $switch
     * @return \Ghalf\RequestAbstract
     */
    public function setRouted($switch) {
        $this->_routed = (bool) $switch;

        return $this;
    }

    /**
     * GET data
     * Index: params->post->get->cookie->server
     *
     * @param string $name
     * @param string $default
     * @return string|number
     */
    public function get($name, $default = null) {
        $result = $default;

        switch (true){
            case isset($this->_params[$name]):
                $result = $this->_params[$name];
                break;
            case isset($this->_post[$name]):
                $result = $this->_post[$name];
                break;
            case isset($this->_query[$name]):
                $result = $this->_query[$name];
                break;
            case $name !== null:
                $result = $this->getCookie($name) ?: $this->getServer($name);
                break;
            default :
                break;
        }

        return $result;
    }

    /**
     * $_ENV
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    public function getEnv($name, $default = null) {
        return filter_input(\INPUT_ENV, $name) ?: $default;
    }

    /**
     * 读取上传文件
     *
     * @param string $name
     * @return array
     */
    public function getFiles($name) {
        return (isset($_FILES[$name])) ? $_FILES[$name] : [];
    }

    /**
     * $_COOKIE
     *
     * @param string $name
     * @return string
     */
    public function getCookie($name = null) {
        return $name ? filter_input(\INPUT_COOKIE, $name) : (filter_input_array(\INPUT_COOKIE, \FILTER_DEFAULT) ?: []);
    }
    
    /**
     * $_SERVER
     *
     * @param string $name
     * @return string
     */
    public function getServer($name = null) {
        return $name ? filter_input(\INPUT_SERVER, $name) : (filter_input_array(\INPUT_SERVER, \FILTER_DEFAULT) ?: []);
    }

    /**
     * POST data / from
     *
     * @param string $name
     * @param string $default
     * @return string|number
     */
    public function getPost($name, $default = null) {
        return (isset($this->_post[$name])) ? $this->_post[$name] : $default;
    }

    /**
     * Query data / from
     *
     * @param string $name
     * @param string $default
     * @return string|number
     */
    public function getQuery($name, $default = null) {
        return (isset($this->_query[$name])) ? $this->_query[$name] : $default;
    }

    /**
     * REQUEST METHOD is Cli
     *
     * @return boolean
     */
    public function isCli() {
        return (bool) ($this->getServer('_') !== null && (int)$this->getServer('argc') > 0);
    }

    /**
     * REQUEST METHOD is Get
     *
     * @return boolean
     */
    public function isGet() {
        return (bool) (0 === strcasecmp('GET', $this->_method));
    }

    /**
     * REQUEST METHOD is Post
     *
     * @return boolean
     */
    public function isPost() {
        return (bool) (0 === strcasecmp('POST', $this->_method));
    }

    /**
     * REQUEST METHOD is DELETE
     *
     * @return boolean
     */
    public function isDelete() {
        return (bool) (0 === strcasecmp('DELETE', $this->_method));
    }

    /**
     * REQUEST METHOD is HEAD
     *
     * @return boolean
     */
    public function isHead() {
        return (bool) (0 === strcasecmp('HEAD', $this->_method));
    }

    public function isOptions() {

    }

    /**
     * REQUEST METHOD is PUT
     *
     * @return boolean
     */
    public function isPut() {
        return (bool) (0 === strcasecmp('PUT', $this->_method));
    }

    /**
     * REQUEST METHOD is XMLHttpRequest
     *
     * @return boolean
     */
    public function isXMLHttpRequest() {
        return (bool) (0 === strcasecmp('XMLHttpRequest', $this->getServer('HTTP_X_REQUESTED_WITH')));
    }

}
