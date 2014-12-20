<?php

namespace Ghalf;

use \Ghalf\Consts;

/**
 * 全局系统配置处理
 */
final class GlobalConfig extends \Ghalf\StdAbstract {

    /**
     * 环境名
     *
     * @var string
     */
    protected $_environ = Consts::ENVIRON;

    /**
     * 应用程序目录路径
     *
     * @var string
     */
    protected $_directory = null;

    /**
     * PHP文件后缀
     *
     * @var string
     */
    protected $_ext = '.php';

    /**
     * 模板文件后缀
     *
     * @var string
     */
    protected $_view_ext = '.phtml';

    /**
     * 引导文件路径
     *
     * @var string
     */
    protected $_bootstrap = null;

    /**
     * 库路径
     *
     * @var string
     */
    protected $_library = null;

    /**
     * 模块
     *
     * @var string
     */
    protected $_modules = array('Index');

    /**
     * 默认 Controller
     *
     * @var string
     */
    protected $_default_controller = 'index';

    /**
     * 默认Action
     *
     * @var string
     */
    protected $_default_action = 'index';

    /**
     * 默认Module
     *
     * @var string
     */
    protected $_default_module = 'Index';

    /**
     * 基础URI， HTTP_HOST
     *
     * @var string
     */
    protected $_base_uri = null;

    /**
     * forward depth,
     * [default: 5]
     *
     * @var int
     */
    protected $_forward_depth = 5;

    /**
     * 类名相关标识是否是后缀式
     *
     * @var boolean
     */
    protected $_name_suffix = true;

    /**
     * 前缀和名字之间的分隔符, 默认为空
     *
     * @var string
     */
    protected $_name_separator = '';

    /**
     * 在出错的时候, 是否抛出异常
     *
     * @var boolean
     */
    protected $_throwException = true;

    /**
     * 是否捕获异常交给Controller
     *
     * @var boolean
     */
    protected $_catchException = false;

    /**
     * 应用程序目录路径
     *
     * @param string $directory
     * @return \Ghalf\Config
     */
    public function setDirectory($directory) {
        $this->_directory = (string) $directory;

        return $this;
    }

    /**
     * 应用程序目录路径
     *
     * @return string
     */
    public function getDirectory() {
        return $this->_directory;
    }

    /**
     * PHP文件后缀
     *
     * @param string $ext
     * @return \Ghalf\Config
     */
    public function setExt($ext) {
        $this->_ext = '.' . ltrim($ext, '.');

        return $this;
    }

    /**
     * PHP文件后缀
     *
     * @return string
     */
    public function getExt() {
        return $this->_ext;
    }

    /**
     * 模板文件后缀
     *
     * @return string
     */
    public function setViewExt($view_ext) {
        $this->_view_ext = '.' . ltrim($view_ext, '.');

        return $this;
    }

    /**
     * 模板文件后缀
     *
     * @return string
     */
    public function getViewExt() {
        return $this->_view_ext;
    }

    /**
     * 引导文件路径
     *
     * @param string $bootstrap
     * @return \Ghalf\Config
     */
    public function setBootstrap($bootstrap) {
        $this->_bootstrap = (string) $bootstrap;

        return $this;
    }

    /**
     * 引导文件路径
     *
     * @return string
     */
    public function getBootstrap() {
        return $this->_bootstrap === null ? $this->_directory : $this->_bootstrap;
    }

    /**
     * 库路径
     *
     * @param string $library
     * @return \Ghalf\Config
     */
    public function setLibrary($library) {
        $this->_library = (string) $library;

        return $this;
    }

    /**
     * 库路径
     *
     * @return string
     */
    public function getLibrary() {
        return $this->_library === null ? $this->_directory . DS . 'library' : $this->_library;
    }

    /**
     * 模块
     *
     * @param string $modules
     * @return \Ghalf\GlobalConfig
     */
    public function setModules($modules){
        $this->_modules = explode(',', $modules);

        return $this;
    }

    /**
     * 模块
     *
     * @return string $modules
     */
    public function getModules(){
        return $this->_modules;
    }

    /**
     * 环境名称
     *
     * @return string $environ
     */
    public function setEnviron($environ){
        $this->_environ = (string)$environ;

        return $this;
    }

    /**
     * 环境名称
     *
     * @return string $environ
     */
    public function getEnviron(){
        return $this->_environ;
    }

    /**
     * 设置默认 Controller
     *
     * @param string $controller
     * @return \Ghalf\Dispatcher
     */
    public function setDefaultController($controller){
        $this->_default_controller = (string)$controller;

        return $this;
    }

    /**
     * 设置默认Action
     *
     * @param string $action
     * @return \Ghalf\Dispatcher
     */
    public function setDefaultAction($action){
        $this->_default_action = (string)$action;

        return $this;
    }

    /**
     * 设置默认Module
     *
     * @param string $module
     * @return \Ghalf\Dispatcher
     */
    public function setDefaultModule($module){
        $this->_default_module = (string)$module;

        return $this;
    }

    /**
     * 默认 Controller
     *
     * @return string
     */
    public function getDefaultController(){
        return $this->_default_controller;
    }

    /**
     * 默认Action
     *
     * @return string
     */
    public function getDefaultAction(){
        return $this->_default_action;
    }

    /**
     * 默认Module
     *
     * @return string
     */
    public function getDefaultModule(){
        return $this->_default_module;
    }

    /**
     * 基础URI， HTTP_HOST
     *
     * @param string $base_uri
     * @return \Ghalf\GlobalConfig
     */
    public function setBaseUri($base_uri){
        $this->_base_uri = (string)$base_uri;

        return $this;
    }

    /**
     * 基础URI， HTTP_HOST
     *
     * @return string
     */
    public function getBaseUri(){
        return $this->_base_uri;
    }

    /**
     * Forward Depth
     *
     * @param int $forward_depth
     * @return \Ghalf\GlobalConfig
     */
    public function setForward_depth($forward_depth){
        $this->_forward_depth = (int)$forward_depth;

        return $this;
    }

    /**
     * Forward Depth
     *
     * @return int
     */
    public function getForward_depth(){
        return $this->_forward_depth;
    }

    /**
     * 类名相关标识是否是后缀式
     *
     * @param int $name_suffix
     * @return \Ghalf\GlobalConfig
     */
    public function setName_suffix($name_suffix){
        $this->_name_suffix = (bool)$name_suffix;

        return $this;
    }

    /**
     * 类名相关标识是否是后缀式
     *
     * @return boolean
     */
    public function getName_suffix(){
        return $this->_name_suffix;
    }

    /**
     * 前缀和名字之间的分隔符, 默认为空
     *
     * @param string $name_separator
     * @return \Ghalf\GlobalConfig
     */
    public function setName_separator($name_separator){
        $this->_name_separator = (string)$name_separator;

        return $this;
    }

    /**
     * 前缀和名字之间的分隔符, 默认为空
     *
     * @return string
     */
    public function getName_separator(){
        return $this->_name_separator;
    }

    /**
     * 在出错的时候, 是否抛出异常
     *
     * @return boolean
     */
    public function getThrowException(){
        return $this->_throwException;
    }

    /**
     * 在出错的时候, 是否抛出异常
     *
     * @param boolean $switch
     * @return \Ghalf\GlobalConfig
     */
    public function setDispatcher_throwException($switch){
        $this->_throwException = (bool)$switch;

        return $this;
    }

    /**
     * 是否捕获异常交给Controller
     *
     * @return boolean
     */
    public function getCatchException(){
        return $this->_catchException;
    }

    /**
     * 是否捕获异常交给Controller
     *
     * @param boolean $switch
     * @return \Ghalf\GlobalConfig
     */
    public function setDispatcher_catchException($switch) {
        $this->_catchException = (bool)$switch;

        return $this;
    }

    public function toArray() {
        return array(
            'directory' => $this->_directory,
            'base_uri'  => $this->_base_uri,
            'ext'       => $this->_ext,
            'view_ext'  => $this->_view_ext,
            'bootstrap' => $this->_bootstrap,
            'library'   => $this->_library,
            'modules'   => $this->_modules,
            'environ'   => $this->_environ
        );
    }

}