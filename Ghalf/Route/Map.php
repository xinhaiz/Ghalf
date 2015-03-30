<?php

namespace Ghalf\Route;

class Map implements \Ghalf\Interfaces\Router {

    protected $_actionPrefer = true;
    protected $_delimeter = null;

    public function __construct($controllerPrefer = false, $delimeter = '_') {
        $this->_actionPrefer = !$controllerPrefer;
        $this->_delimeter      = (string)$delimeter;
    }

    public function route(\Ghalf\RequestAbstract $request) {
        $uri = $request->getRequestUri();

        if (strlen($this->_delimeter) === 1) {
            $pos    = strpos($uri, $this->_delimeter);
            $params = explode('/', trim(substr($uri, $pos + 1), '/'));
            $uri    = substr($uri, 0, $pos);

            for($i = 0, $t = count($params); $i < $t; $i++){
                $request->setParam($params[$i], (isset($params[++$i]) ? $params[$i] : null));
            }
        }

        $ctl_route = str_replace('/', '_', trim($uri, '/'));

        $request->setModuleName(null);

        if ($this->_actionPriority === true) {
            $request->setActionName($ctl_route);
        } else {
            $request->setControllerName($ctl_route);
        }

        $request->setRouted(true);
        return true;
    }
    
}
