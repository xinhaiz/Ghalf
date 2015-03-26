<?php

namespace Ghalf;

use \Ghalf\Consts;
use \Ghalf\Exception\LoadControllerFailed;
use \Ghalf\Exception\LoadActionFailed;
use \Ghalf\Exception\DispatchFailed;

final class Dispatcher {

    use \Ghalf\Instance;

    /**
     * 全局配置
     *
     * @var \Ghalf\GlobalConfig
     */
    protected $_global = null;

    /**
     * 应用入口
     *
     * @var \Ghalf\Application
     */
    protected $_application = null;

    /**
     * 请求实例
     *
     * @var \Ghalf\RequestAbstract
     */
    protected $_request = null;

    /**
     * 路由
     *
     * @var \Ghalf\Interfaces\Router
     */
    protected $_router = null;

    /**
     * 视图
     *
     * @var \Ghalf\View
     */
    protected $_view = null;

    /**
     * 视图处理 - 是否开启视图
     *
     * @var boolean
     */
    protected $_disableView = false;

    /**
     * 视图处理 - 是否开启视图渲染
     *
     * @var boolean
     */
    protected $_autoRender = true;

    /**
     * 资源管理器
     *
     * @var \Ghalf\Response\Http
     */
    protected $_response = null;

    /**
     * 是否返回Response对象
     *
     * @var boolean
     */
    protected $_autoResponse = false;

    /**
     * 切换自动响应
     *
     * @var boolean
     */
    protected $_autoFlush = false;

    /**
     * 定义前往目标
     *
     * @var array
     */
    protected $_forward = [];

    public function __construct() {
        $this->_response = \Ghalf\Response\Http::getInstance();
        $this->_global   = \Ghalf\Register::get(Consts::GC);

        if (!$this->_global instanceof \Ghalf\GlobalConfig) {
            throw new \Ghalf\Exception("invalid global config");
        }
    }

    private function __clone() {}
    private function __sleep() {}

    /**
     * 调度
     */
    public function dispatch($request = null) {
        if ($request instanceof \Ghalf\RequestAbstract) {
            $this->setRequest($request);
        }

        $events  = \Ghalf\Events::getInstance();
        $request = $this->getRequest();
        $depth   = $this->_global->getForward_depth();

        $this->initView();
        $this->dispatheExceptionHandler();
        $this->dispatheRouteHandler();
        $this->setForward($request);

        $events->onStartup('dispatchLoop', $request);

        while (($that = array_shift($this->_forward)) && $depth-- > 0) {
            if (!$that instanceof \Ghalf\RequestAbstract || $that->isRouted() === false) {
                $that->setDispatched(false);
                break;
            }

            $this->setRequest($that);
            $events->onStartup('dispatch', $that);
            $this->dispatheHandler();
            $this->dispatheViewHandler();
            $events->onShutdown('dispatch', $that);
            $that->setDispatched(true);
        }
        
        $events->onShutdown('dispatchLoop', $request);

        $http = \Ghalf\Response\Http::getInstance();
        $response = ($this->_response === true ? $response : (bool)$http->response());
        $http->clearBody();

        return $response;
    }

    /**
     * 资源分发管控
     *
     * @return boolean
     * @throws \Ghalf\Exception
     */
    protected function dispatheHandler() {
        $global  = $this->_global;
        $request = $this->getRequest();
        $action  = $request->getActionName();
        $cName   = $request->getControllerName();
        $module  = $request->getModuleName();
        $separ   = $global->getName_separator();
        $tplDir  = Loader::getInstance()->filePath(sprintf('%sviews', $global->getDirectory()), null);

        if(!empty($module) && in_array($global, $global->getModules())){
            $tplDir .= DS . $module;
        }

        $this->_view->setScriptPath($tplDir . DS);

        $c = $global->getName_suffix() === false
                ? \sprintf(Consts::PREFIX_CONTROLLER, $separ, ucfirst($cName))
                : \sprintf(Consts::SUFIXX_CONTROLLER, ucfirst($cName), $separ);

        $controller = new $c($request, $this->_response, $this->_view);

        if (!$controller instanceof \Ghalf\ControllerAbstract) {
            throw new LoadControllerFailed(substr($c, 1) . 'must extended \Ghalf\ControllerAbstract');
        }

        if($controller->init() === false){
            $this->_autoRender = false;
            return false;
        }

        $a = $global->getName_suffix() === false
                ? \sprintf(Consts::PREFIX_ACTION, $separ, ucfirst($action))
                : \sprintf(Consts::SUFIXX_ACTION, $action, $separ);

        if (!method_exists($controller, $a)) {
            throw new LoadActionFailed('method "' . $a . '" not found in ' . substr($c, 1));
        }

        $render = call_user_func_array(array($controller, $a), (array)$request->getParams());

        if ($render === false) {
            $this->_autoRender = false;
        }

        return true;
    }

    /**
     * 资源渲染管控
     *
     * @return boolean
     * @throws \Ghalf\Exception
     */
    protected function dispatheViewHandler(){
        if ($this->_autoRender === true && $this->_disableView === false) {
            $global  = $this->_global;
            $request = $this->getRequest();
            $action  = $request->getActionName();
            $tpl     = $request->getControllerName() . DS . $action . $global->getViewExt();

            if($this->_autoFlush === true){
                $this->_view->display($tpl);
                return true;
            }

            $content = $this->_view->render($tpl);
            $this->_response->prepenBody($content, 'body');
        }

        return true;
    }

    /**
     * 异常处理
     *
     * @return boolean
     */
    protected function dispatheExceptionHandler(){
        $global = $this->_global;

        if ($global->getCatchException() === true) {
            $separ = $global->getName_separator();

            if($global->getName_suffix() === false){
                $c = \sprintf(Consts::PREFIX_CONTROLLER, $separ, Consts::ERROR_CONTROLLER);
                $a = \sprintf(Consts::PREFIX_ACTION, $separ, Consts::ERROR_ACTION);
            } else {
                $c = \sprintf(Consts::SUFIXX_CONTROLLER, Consts::ERROR_CONTROLLER, $separ);
                $a = \sprintf(Consts::SUFIXX_ACTION, Consts::ERROR_ACTION, $separ);
            }

            $controller = new $c($this->_request, $this->_response, $this->_view);
            \set_exception_handler(array($controller, $a));
        }

        return true;
    }

    /**
     * 处置路由
     *
     * @return boolean
     * @throws \Ghalf\Exception\RouterFailed
     */
    protected function dispatheRouteHandler(){
        $events  = \Ghalf\Events::getInstance();
        $request = $this->getRequest();

        $events->onStartup('router', $request);
        $this->getRouter()->route($request);

        if($request->isRouted() === false){
            throw new \Ghalf\Exception\RouterFailed('Routing request faild');
        }

        $events->onShutdown('router', $request);

        return true;
    }

    /**
     * 设置Application
     *
     * @param \Ghalf\Application $app
     * @return \Ghalf\Dispatcher
     */
    public function setApplication(\Ghalf\Application $app){
        $this->_application = $app;

        return $this;
    }

    /**
     * Application
     *
     * @return \Ghalf\Application
     */
    public function getApplication(){
        return $this->_application;
    }

    /**
     * 登记前往的下一个目标
     *
     * @param \Ghalf\RequestAbstract $forward
     * @return \Ghalf\Dispatcher
     */
    public function setForward(\Ghalf\RequestAbstract $forward) {
        array_push($this->_forward, $forward);

        return $this;
    }

    /**
     * 设置默认 Controller
     *
     * @param string $controller
     * @return \Ghalf\Dispatcher
     */
    public function setDefaultController($controller) {
        $this->_global->setDefaultController($controller);

        return $this;
    }

    /**
     * 设置默认Action
     *
     * @param string $action
     * @return \Ghalf\Dispatcher
     */
    public function setDefaultAction($action) {
        $this->_global->setDefaultAction($action);

        return $this;
    }

    /**
     * 设置默认Module
     *
     * @param string $module
     * @return \Ghalf\Dispatcher
     */
    public function setDefaultModule($module) {
        $this->_global->setDefaultModule($module);

        return $this;
    }

    /**
     * 注册插件
     *
     * @param \Ghalf\PluginAbstract $plugin
     * @return \Ghalf\Dispatcher
     */
    public function registerPlugin(\Ghalf\PluginAbstract $plugin) {
        $events = \Ghalf\Events::getInstance()->listen();

        $events->add('startup', 'router', array($plugin, 'routerStartup'));
        $events->add('shutdown', 'router', array($plugin, 'routerShutdown'));
        $events->add('startup', 'dispatchLoop', array($plugin, 'dispatchLoopStartup'));
        $events->add('shutdown', 'dispatchLoop', array($plugin, 'dispatchLoopShutdown'));
        $events->add('startup', 'dispatch', array($plugin, 'preDispatch'));
        $events->add('shutdown', 'dispatch', array($plugin, 'postDispatch'));

        return $this;
    }

    /**
     * 设置应用目录路径
     *
     * @param string $directory
     */
    public function setAppDirectory($directory) {
        $this->_global->setDirectory($directory);
    }

    /**
     * 请求实例
     *
     * @param \Ghalf\RequestAbstract $request
     * @return \Ghalf\Dispatcher
     */
    public function setRequest(\Ghalf\RequestAbstract $request) {
        $this->_request = $request;

        return $this;
    }

    /**
     * 请求实例
     *
     * @return \Ghalf\RequestAbstract
     */
    public function getRequest() {
        if (!$this->_request instanceof \Ghalf\RequestAbstract) {
            $request = new \Ghalf\Request\Http();

            $global = $this->_global;
            $request->setModuleName($global->getDefaultModule());
            $request->setControllerName($global->getDefaultController());
            $request->setActionName($global->getDefaultAction());

            if($request->getBaseUri() === null){
                $request->setBaseUri($global->getBaseUri());
            }

            $this->_request = $request;
        }

        return $this->_request;
    }

    /**
     * 路由
     *
     * @return \Ghalf\Router
     */
    public function getRouter() {
        if (!$this->_router instanceof \Ghalf\Router) {
            $this->_router = new \Ghalf\Router();
        }

        return $this->_router;
    }

    /**
     * 关闭视图
     *
     * @return \Ghalf\Dispatcher
     */
    public function disableView() {
        $this->_disableView = true;

        return $this;
    }

    /**
     * 开启视图
     *
     * @return \Ghalf\Dispatcher
     */
    public function enableView() {
        $this->_disableView = false;

        return $this;
    }

    /**
     * 是否注册Module
     *
     * @param string $name
     * @return boolean
     * @throws \Ghalf\Exception
     */
    public function isModuleName($name) {
        $source = sprintf('.%s.', implode('.', $this->_global->getModules()));
        $target = sprintf('.%s.', ucfirst($name));

        return (strpos($source, $target) === false) ? false : true;
    }

    /**
     * 切换自动响应
     *
     * @param bool $switch
     * @return \Ghalf\Dispatcher
     */
    public function autoFlush($switch) {
        $this->_autoFlush = (bool)$switch;

        return $this;
    }

    /**
     * 是否返回Response对象
     *
     * @param boolean $switch
     * @return \Ghalf\Dispatcher
     */
    public function returnResponse($switch) {
        $this->_autoResponse = (bool) $switch;

        return $this;
    }

    /**
     * 是否开启自动渲染视图
     *
     * @param boolean $switch
     * @return \Ghalf\Dispatcher
     */
    public function autoRender($switch) {
        $this->_autoRender = (bool) $switch;

        return $this;
    }

    /**
     * 配置相关视图参数
     *
     * @return \Ghafl\Interfaces\View
     */
    public function initView($tpl_dir = null) {
        if (!$this->_view instanceof \Ghalf\Interfaces\View) {
            $this->setView(new \Ghalf\View());
        }

        if (!empty($tpl_dir) && $this->_view instanceof \Ghalf\Interfaces\View) {
            $this->_view->setScriptPath($tpl_dir);
        }

        return $this->_view;
    }

    /**
     * 设置视图引擎
     *
     * @param \Ghafl\Interfaces\View $view
     * @return \Ghalf\Dispatcher
     */
    public function setView(\Ghalf\Interfaces\View $view) {
        if ($this->_disableView === false) {
            $this->_view = $view;
        }

        return $this;
    }

    /**
     * 设置错误处理函数
     *
     * @param callable $error_handler
     * @param int $error_code
     * @return \Ghalf\Dispatcher
     */
    public function setErrorHandler($error_handler, $error_code = null) {
        if ($error_code === null) {
            $error_code = E_ALL | E_STRICT;
        }

        \set_error_handler($error_handler, $error_code);

        return $this;
    }

    /**
     * 设置是否抛出异常
     *
     * @param boolean $switch
     * @return \Ghalf\Dispatcher
     */
    public function throwException($switch) {
        $this->_global->setThrowException($switch);

        return $this;
    }

    /**
     * 设置是否捕捉异常，交给Controller
     *
     * @param type $switch
     * @return \Ghalf\Dispatcher
     */
    public function catchException($switch) {
        $this->_global->setCatchException($switch);

        return $this;
    }

}