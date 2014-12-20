<?php

namespace Ghalf\Route;

class Simple implements \Ghalf\Interfaces\Router {

    protected $_module     = null;
    protected $_controller = null;
    protected $_action     = null;

    public function __construct($module, $controller, $action) {
        $this->_module     = (string)$module;
        $this->_controller = (string)$controller;
        $this->_action     = (string)$action;
    }

    public function route(\Ghalf\RequestAbstract $request) {
        if (empty($_SERVER['QUERY_STRING'])) {
            return false;
        }

        $query    = explode('&', $_SERVER['QUERY_STRING']);
        $items    = array();
        $isRouted = false;

        foreach ($query as $value) {
            $item            = explode('=', $value);
            $items[$item[0]] = $item[1];
        }

        if (!isset($items[$this->_module]) || \Ghalf\Dispatcher::getInstance()->isModuleName($items[$this->_module])) {
            $items[$this->_module] = null;
        }

        $request->setModuleName($items[$this->_module]);

        if (isset($items[$this->_controller])) {
            $request->setControllerName($items[$this->_controller]);
            $isRouted = true;
        }

        if ($isRouted === true && isset($items[$this->_action])) {
            $request->setActionName($items[$this->_action]);
        }

        $request->setRouted($isRouted);

        return $isRouted;
    }

}
