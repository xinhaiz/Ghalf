<?php

namespace Ghalf;

abstract class PluginAbstract {

    final public function __construct() {

    }

    public function routerStartup(\Ghalf\RequestAbstract $request) {
        
    }

    public function routerShutdown(\Ghalf\RequestAbstract $request) {

    }

    public function dispatchLoopStartup(\Ghalf\RequestAbstract $request) {

    }

    public function dispatchLoopShutdown(\Ghalf\RequestAbstract $request) {

    }

    public function preDispatch(\Ghalf\RequestAbstract $request) {

    }

    public function postDispatch(\Ghalf\RequestAbstract $request) {

    }

}
