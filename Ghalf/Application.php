<?php

namespace Ghalf;

use \Ghalf\Consts;
use \Ghalf\Loader;

final class Application {

    /**
     * 配置
     *
     * @var Config
     */
    protected $_config = null;

    /**
     * 载入控制器
     *
     * @var \Ghalf\Loader;
     */
    protected $_loader = null;

    /**
     * 调度
     *
     * @var \Ghalf\Dispatcher
     */
    protected $_dispatcher = null;

    public function __construct($mixed) {
        !defined('DS') && define('DS', DIRECTORY_SEPARATOR);
        !defined('APP_PATH') && define('APP_PATH', realpath('..' . DS));

        require(APP_PATH . DS . 'Ghalf' . DS . 'Loader.php');
        Loader::getInstance();

        $this->_config = $mixed;

        $application = $this->getConfig()->get('application')->toArray();
        \Ghalf\Register::set(Consts::GC, new \Ghalf\GlobalConfig($application));

        $this->_dispatcher = \Ghalf\Dispatcher::getInstance();
        $this->_dispatcher->setApplication($this);
    }

    /**
     * 调度执行
     */
    public function run() {
        $dispatcher = $this->_dispatcher;
        $dispatcher->dispatch();

        if ($dispatcher->getRequest()->isDispatched() === false) {
            throw new \Ghalf\Exception\DispatchFailed('Dispatch failed');
        }
    }

    /**
     * 引导相关处理
     *
     * @throws \Ghalf\Exception
     * @return \Ghalf\Application
     */
    public function bootstrap() {
        $global = \Ghalf\Register::get(Consts::GC);
        $boot   = Loader::getInstance()->import($global->getBootstrap() . 'Bootstrap');

        if ($boot === true) {
            $bootstrap = new \Bootstrap();

            if (!$bootstrap instanceof \Ghalf\BootstrapAbstract) {
                throw new \Ghalf\Exception('Invalid Bootstrap, must extend \Ghalf\BootstrapAbstract');
            }

            $dispatcher = $this->_dispatcher;
            $reflection = new \ReflectionClass($bootstrap);

            foreach ($reflection->getMethods() as $method) {
                $fun = $method->getName();

                if (strpos($fun, '_init') === 0 && strlen($fun) > 5) {
                    call_user_func_array(array($bootstrap, $fun), array($dispatcher));
                }
            }
        }

        return $this;
    }

    /**
     * 设置环境
     *
     * @param string $environ
     */
    public function setEnviron($environ) {
        \Ghalf\Register::get(Consts::GC)->setEnviron($environ);

        return $this;
    }

    /**
     * 环境
     *
     * @return string $environ
     */
    public function getEnviron() {
        return \Ghalf\Register::get(Consts::GC)->getEnviron();
    }

    /**
     * 配置
     *
     * @return \Ghalf\Config\Ini
     * @throws \Ghalf\Exception
     */
    public function getConfig() {
        if (!$this->_config instanceof \Ghalf\Config\Ini) {
            $this->_config = new \Ghalf\Config\Ini($this->_config);
        }

        return $this->_config;
    }

}
