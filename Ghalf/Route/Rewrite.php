<?php

namespace Ghalf\Route;

class Rewrite implements \Ghalf\Interfaces\Router {

    const vars = ':';
    const regs = '*';

    protected $_query  = null;
    protected $_target = null;

    public function __construct($query_string, array $target) {
        $this->setParams($query_string, $target);
    }

    /**
     * 参数设置
     *
     * @param string $query_string
     * @param array $target
     */
    public function setParams($query_string, array $target){
        $this->_query = explode('/', trim($query_string, '/'));
        $this->_target = (array)$target;
    }

    public function route(\Ghalf\RequestAbstract $request, \Ghalf\Router $router) {
        $params = explode('/', $request->getRequestUri());
        $query  = $this->_query;
        $count  = count($params);
        $size   = count($query);

        if ($size > $count) {
            return false;
        }

        $index = 0;
        $isContinued = false;

        for (; $index < $size; $index++) {
            $vals = $query[$index];

            if ($vals === self::regs) {
                $isContinued = true;
                break;
            }

            $pval = $params[$index];

            if (strpos($vals, self::vars) === 0) {
                $request->setParam(substr($vals, 1), $pval);
            }else{
                if(strcasecmp($pval, $vals) !== 0){
                    return false;
                }
            }
        }

        if($isContinued === true){
            for (; $index < $count; $index++) {
                if(empty($params[$index])){
                    $index++;
                    continue;
                }

                $request->setParam($params[$index], isset($params[++$index]) ? $params[$index] : null);
            }
        }

        $target = $this->_target;

        if(!isset($target['module']) || !$router->isModuleName($target['module'])) {
            $target['module'] = null;
        }

        $request->setModuleName($target['module']);

        if(isset($target['controller'])){
            $request->setControllerName($target['controller']);
        }

        if(isset($target['action'])){
            $request->setActionName($target['action']);
        }

        $request->setRouted(true);

        return true;
    }

}
