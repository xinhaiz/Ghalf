<?php

namespace Ghalf\Route;

class Regex implements \Ghalf\Interfaces\Router {

    protected $_expression = null;
    protected $_target     = null;
    protected $_mapper     = null;

    public function __construct($expression, array $target, array $mapper = null) {
        $this->setParams($expression, $target, $mapper);
    }

    /**
     * 参数设置
     *
     * @param string $expression
     * @param array $target
     * @param array $mapper
     */
    public function setParams($expression, array $target, array $mapper = null){
        $this->_expression = (string) $expression;
        $this->_target     = (array) $target;

        if(!empty($mapper)){
            $this->_mapper = (array) $mapper;
        }
    }

    public function route(\Ghalf\RequestAbstract $request) {
        $uri = $request->getRequestUri();
        $tmp = [];

        preg_match($this->_expression, '/' . ltrim($uri, '/'), $tmp);

        if (empty($tmp) || !isset($tmp[1])) {
            return false;
        }

        unset($tmp[0]);

        $reg = [];

        if(!empty($this->_mapper)){
            $mapper = $this->_mapper;

            foreach ($tmp as $index => $value){
                $key = (isset($mapper[$index]) ? $mapper[$index] : $index);
                $reg[$key] = $value;
            }
        }else{
            $reg = $tmp;
        }

        foreach ($reg as $key => $value){
            $request->setParam($key, $value);
        }

        $target = $this->_target;

        if (!isset($target['module'])) {
            $target['module'] = 'index';
        }

        $request->setModuleName($target['module']);

        if (isset($target['controller'])) {
            $request->setControllerName($target['controller']);
        }

        if (isset($target['action'])) {
            $request->setActionName($target['action']);
        }

        $request->setRouted(true);

        return true;
    }

}
