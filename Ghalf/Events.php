<?php

namespace Ghalf;

final class Events {

    private static $_instance = null;

    /**
     * 当前事件监听端口
     *
     * @var int
     */
    private $_port = 0;

    /**
     * 事件监听端口组(Using)
     *
     * @var array
     */
    private $_use_port = [];

    /**
     * 响应事件组
     *
     * @var array
     */
    private $_events = [];

    /**
     * 支持的事件
     *
     * @var array
     */
    private $_onEvent = array('startup' => 1, 'shutdown' => 1, 'change' => 1);

    public function __construct() {}

    private function __clone(){}
    private function __sleep(){}
    private function __wakeup(){}

    public static function getInstance(){
        if(!self::$_instance instanceof self){
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 事件监听端口
     *
     * @param int $port
     */
    public function listen($port = 0) {
        $this->_port = $port;
        $this->_use_port[] = $port;

        return $this;
    }

    /**
     * 切换事件监听端口
     *
     * @param int $port
     */
    public function switchListen($port){
        $this->_port = (int)$port;

        return $this;
    }

    /**
     * 名称
     *
     * @param string $event_name
     * @param string $process_name
     * @return string
     */
    protected function name($event_name, $process_name){
        return sprintf('%s_%s', $event_name, $process_name);
    }

    /**
     * 登记事件
     *
     * @param string $event_name
     * @param string $process_name
     * @param object|function $call
     * @return \Ghalf\Events
     */
    public function add($event_name, $process_name, $call) {

        if (is_callable($call)) {
            $this->_events[$this->_port][$this->name($event_name, $process_name)] = $call;
        }

        return $this;
    }

    /**
     * 获取事件
     *
     * @param string $event_name
     * @param int $port
     * @return object|function|null
     */
    public function get($event_name){
        return ($this->has($event_name) === true) ? $this->_events[$this->_port][$event_name] : null;
    }

    /**
     * 移除事件
     *
     * @param string $event_name
     * @param int $port
     * @return boolean
     */
    public function remove($event_name){
        if($this->has($event_name) === true){
            unset($this->_events[$this->_port][$event_name]);

            return true;
        }

        return false;
    }

    /**
     * 事件检测
     *
     * @param string $event_name
     * @return bool
     */
    public function has($event_name){
        return (bool)isset($this->_events[$this->_port][$event_name]);
    }

    /**
     * 启动前触发事件
     *
     * @param string $process_name
     * @param any $params
     * @return boolean
     */
    public function onStartup($process_name = null, $params = null){
        return $this->on('startup', $process_name, $params);
    }

    /**
     * 结束后触发事件
     *
     * @param string $process_name
     * @param any $params
     * @return boolean
     */
    public function onShutdown($process_name = null, $params = null){
        return $this->on('shutdown', $process_name, $params);
    }

    /**
     * 调整/修改时触发事件
     *
     * @param string $process_name
     * @param any $params
     * @return boolean
     */
    public function onChange($process_name = null, $params = null){
        return $this->on('change', $process_name, $params);
    }

    /**
     * 事件公共处理
     *
     * @param string $process_name
     * @param any $params
     * @return boolean
     */
    public function on($event_name, $process_name, $params = null){
        $use_port = array_flip(array_flip($this->_use_port));

        foreach ($use_port as $port){
            $this->_port = $port;

            $event = $this->get($this->name($event_name, $process_name));

            if (!empty($event) && is_callable($event)) {
                call_user_func($event, $params);
            }
        }

        return true;
    }
}
