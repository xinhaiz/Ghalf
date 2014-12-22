<?php

namespace Ghalf;

use \Ghalf\HttpCode;

class ControllerAbstract {

    /**
     * 基础视图引擎实例
     *
     * @var \Ghalf\Interfaces\View
     */
    protected $_view = null;

    /**
     * 基础请求实例
     *
     * @var \Ghalf\RequestAbstract
     */
    private $_request = null;

    /**
     * 基础资源实例
     *
     * @var \Ghalf\ResponseAbstract
     */
    private $_response = null;

    /**
     * 调用参数
     *
     * @var array
     */
    private $_invokeArgs = null;

    /**
     * @param \Ghalf\RequestAbstract $request
     * @param \Ghalf\ResponseAbstract $response
     * @param \Ghalf\Interfaces\View $view
     * @param array $invokeArgs
     */
    final public function __construct(\Ghalf\RequestAbstract $request, \Ghalf\ResponseAbstract $response, \Ghalf\Interfaces\View $view, array $invokeArgs = NULL) {
        $this->_view     = $view;
        $this->_response = $response;
        $this->_request  = $request;

        if (!empty($invokeArgs)) {
            $this->_invokeArgs = $invokeArgs;
        }
    }

    /**
     * 每个 Action 执行前处理
     */
    public function init() {

    }

    /**
     * 处理View相关
     */
    public function initView() {

    }

    /**
     * 调用参数
     *
     * @return array
     */
    public function getInvokeArg() {
        return $this->_invokeArgs;
    }

    /**
     * 请求实例
     *
     * @return \Ghalf\RequestAbstract
     */
    public function getRequest() {
        return $this->_request;
    }

    /**
     * 模块名称（当前）
     *
     * @return string
     */
    public function getModuleName() {
        return $this->_request->getModuleName();
    }

    /**
     * Get 视图引擎
     *
     * @return　\Ghalf\Interfaces\View
     */
    public function getView() {
        return $this->_view;
    }

    /**
     * 当前视图引擎的模板文件基目录
     *
     * @param type $view_directory
     * @return \Ghalf\ControllerAbstract
     */
    public function setViewPath($view_directory) {
        $this->_view->setScriptPath($view_directory);

        return $this;
    }

    /**
     * 当前视图引擎的模板文件基目录
     *
     * @return string
     */
    public function getViewPath() {
        return $this->_view->getScriptPath();
    }

    /**
     * 资源管理
     *
     * @return \Ghalf\ResponseAbstract
     */
    public function getResponse() {
        return $this->_response;
    }

    /**
     * 渲染一个视图模板, 得到结果
     *
     * @param string $view_path
     * @param array $vars
     * @return string|null
     */
    public function render($view_path, array $vars = null) {
        $this->_view->render($view_path, $vars);

        return $this;
    }

    /**
     * 渲染一个视图模板, 并直接输出给请求端
     *
     * @param type $view_path
     * @param array $vars
     */
    public function display($view_path, array $vars = null) {
        $this->_view->display($view_path, $vars);

        return $this;
    }

    /**
     * 登记下一个前往目标
     *
     * @param array $forward
     * @return \Ghalf\ControllerAbstract
     */
    public function forward() {
        $args   = func_get_args();
        $num    = func_num_args();
        $that   = $this->_request;
        $params = [];

        if ($num > 4) {
            return false;
        }

        $a = $that->getActionName();
        $c = $that->getControllerName();
        $m = $that->getModuleName();

        $last = array_pop($args);

        if (is_array($last)) {
            $params = $last;
            $a      = array_pop($args);
        } else {
            $a = $last;
        }

        if ($num > 1) {
            if ($num > 2) {
                $c = array_pop($args);
                $m = array_pop($args);
            } else {
                $c = array_pop($args);
            }
        }

        $request = new \Ghalf\Request\Simple($m, $c, $a, $params);
        $request->setRouted(true);
        $request->setBaseUri($that->getBaseUri());

        \Ghalf\Dispatcher::getInstance()->setForward($request);

        return $this;
    }

    /**
     * 重定向
     *
     * @param string $url
     * @param int $http_code
     * @return boolean
     */
    public function redirect($url, $http_code = HttpCode::HTTP_MOVED_PERMNENTLY) {
        header('Location: ' . $url, false, $http_code);

        return true;
    }

}
