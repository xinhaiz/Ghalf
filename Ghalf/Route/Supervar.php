<?php

namespace Ghalf\Route;

class Supervar implements \Ghalf\Interfaces\Router {

    protected $_query = null;

    public function __construct($query) {
        $this->_query = (string)$query;
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

        if (!isset($items[$this->_query])) {
            return false;
        }

        $params = explode('/', trim($items[$this->_query], '/'));
        $index  = 0;
        $size   = count($params);

        if($size < 2){
            return false;
        }

        $module = $params[$index++];

        if(!\Ghalf\Dispatcher::getInstance()->isModuleName($module) === true){
            $request->setControllerName($module);
            $isRouted = true;
            $module = null;
        }elseif(isset($params[$index])){
            $request->setControllerName($params[$index++]);
            $isRouted = true;
        }

        if($isRouted === true){
            $request->setModuleName($module);

            if(isset($params[$index])){
                $request->setActionName($params[$index++]);
            }

            for($i = $index; $i < $size; $i++){
                $request->setParam($params[$i], isset($params[++$i]) ? $params[$i] : null);
            }
        }

        $request->setRouted($isRouted);

        return $isRouted;
    }

}

