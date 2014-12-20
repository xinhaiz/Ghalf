<?php

namespace Ghalf\Route;

class Statics implements \Ghalf\Interfaces\Router {

    public function __construct() {}

    public function route(\Ghalf\RequestAbstract $request) {
        $params   = explode('/', $request->getRequestUri());
        $index    = 0;
        $isRouted = false;
        $size     = count($params);

        if ($size < 1) {
            $request->setRouted(true);
            return true;
        }

        $module = $params[$index++];

        if (\Ghalf\Dispatcher::getInstance()->isModuleName($module) === false) {
            $request->setControllerName($module);
            $isRouted = true;
            $module   = null;
        }elseif (isset($params[$index])) {
            $request->setControllerName($params[$index++]);
            $isRouted = true;
        }

        if ($isRouted === true) {
            $request->setModuleName($module);

            if (isset($params[$index])) {
                $request->setActionName($params[$index++]);
            }

            for ($i = $index; $i < $size; $i++) {
                $request->setParam($params[$i], isset($params[++$i]) ? $params[$i] : null);
            }
        }

        $request->setRouted($isRouted);

        return $isRouted;
    }

}
