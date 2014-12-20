<?php

namespace Ghalf;

final class Router implements \Ghalf\Interfaces\Router {

    /**
     * 当前生效的路由协议
     *
     * @var string
     */
    protected $_current_route = null;

    /**
     * 路由协议集合
     *
     * @var string
     */
    protected $_routes = [];

    /**
     * 路由协议配置
     *
     * @var array
     */
    protected $_route_config = [];

    /**
     * 路由协议对象
     *
     * @var array
     */
    protected $_routers = [];

    public function __construct() {}

    /**
     * 添加路由协议
     *
     * @param string $name
     * @param \Ghalf\Interfaces\Router $router
     * @return \Ghalf\Router
     */
    public function addRoute($name, \Ghalf\Interfaces\Router $router) {
        $this->_routes[$name] = $router;

        return $this;
    }

    /**
     * 增加路由配置
     *
     * @param $config
     * @return \Ghalf\Router
     */
    public function addConfig($config) {
        $this->_route_config[] = $config;

        return $this;
    }

    /**
     * 所有路由协议
     *
     * @return array
     */
    public function getRoutes() {
        return $this->_routes;
    }

    /**
     * 获取路由中$name的协议
     *
     * @param string $name
     * @return \Ghalf\Interfaces\Router｜null
     */
    public function getRoute($name) {
        return (isset($this->_routes[$name])) ? $this->_routes[$name] : null;
    }

    /**
     * 当前路由协议名称
     *
     * @return string
     */
    public function getCurrentRoute() {
        return $this->_current_route;
    }

    /**
     * 是否注册Module
     *
     * @param string $name
     * @return boolean
     * @throws \Ghalf\Exception
     */
    public function isModuleName($name) {
        return \Ghalf\Dispatcher::getInstance()->isModuleName($name);
    }

    /**
     * 路由解析
     *
     * @return boolean
     */
    public function route(\Ghalf\RequestAbstract $request) {
        if(trim($request->getRequestUri(), '/') === ''){
            $request->setRouted(true);
            return true;
        }

        $cRoutes = $this->_route_config;

        if (!empty($cRoutes) && is_array($cRoutes)) {
            foreach ($cRoutes as $route) {
                if ($this->parseRoute($route, $request) === true) {
                    return true;
                }
            }
        }

        $default = new \Ghalf\Route\Statics();

        if ($default instanceof \Ghalf\Interfaces\Router && $default->route($request) === true) {
            $this->_current_route = 'default';
            return true;
        }

        return false;
    }

    /**
     * 处理路由配置
     *
     * @param \Ghalf\Config\Ini $route
     * @return boolean
     */
    protected function parseRoute(\Ghalf\Config\Ini $route, \Ghalf\RequestAbstract $request) {
        $routes = $route->get('routes')->toArray();

        foreach ($routes as $router) {
            if (!isset($router['type'])) {
                continue;
            }

            $routeType = strtolower($router['type']);
            $funcName  = '_' . $routeType;

            if (!method_exists($this, $funcName) || call_user_func_array(array($this, $funcName), array($router)) === false) {
                continue;
            }

            $router = isset($this->_routers[$routeType]) ? $this->_routers[$routeType] : null;

            if ($router instanceof \Ghalf\Interfaces\Router && $router->route($request) === true) {
                $this->_current_route = $routeType;
                return true;
            }
        }

        return false;
    }

    /**
     * Regex
     *
     * @param array $route
     * @return boolean
     */
    private function _regex($route) {
        if (!isset($route['match']) || !isset($route['route'])) {
            return false;
        }

        $routers = $this->_routers;
        $mapper  = (isset($route['map'])) ? $route['map'] : null;

        if (!isset($routers['regex']) || !$routers['regex'] instanceof \Ghalf\Interfaces\Router) {
            $this->_routers['regex'] = new \Ghalf\Route\Regex($route['match'], $route['route'], $mapper);
        } else {
            $regex = $routers['regex'];
            $regex->setParams($route['match'], $route['route'], $mapper);
        }

        return true;
    }

    /**
     * rewrite
     *
     * @param array $route
     * @return boolean
     */
    private function _rewrite($route) {
        if (!isset($route['query_string']) || !isset($route['target'])) {
            return false;
        }

        $routers = $this->_routers;

        if (!isset($routers['rewrite']) || !$routers['rewrite'] instanceof \Ghalf\Interfaces\Router) {
            $this->_routers['rewrite'] = new \Ghalf\Route\Rewrite($route['query_string'], $route['target']);
        } else {
            $rewrite = $routers['rewrite'];
            $rewrite->setParams($route['query_string'], $route['target']);
        }

        return true;
    }

}
