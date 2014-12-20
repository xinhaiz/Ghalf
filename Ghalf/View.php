<?php

namespace Ghalf;

use \Ghalf\Loader;
use \Ghalf\Exception\LoadViewFailed;

class View implements \Ghalf\Interfaces\View {

    /**
     * 属性变量
     *
     * @var array
     */
    protected $_tpl_vars = array();

    /**
     * 当前视图引擎的模板文件基目录
     *
     * @var string
     */
    protected $_script_path = null;

    public function __construct() {
    }

    /**
     * 变量值
     *
     * @param string $name
     */
    public function get($name){
        return (isset($this->$name)) ? $this->$name : null;
    }

    /**
     * 属性变量分配
     *
     * @param string $name
     * @param string $value
     * @return \Ghalf\View
     */
    public function assign($name, $value = null) {
        if(!empty($name)){
            $name = (is_array($name)) ? $name : array($name => $value);

            foreach ($name as $key => $val){
                $this->_tpl_vars[$key] = $val;
            }
        }

        return $this;
    }

    /**
     * 渲染一个视图模板, 并直接输出给请求端
     *
     * @param type $view_path
     * @param array $vars
     */
    public function display($view_path, array $vars = null) {
        echo $this->render($view_path, $vars);
    }

    /**
     * 渲染一个视图模板, 得到结果
     *
     * @param string $view_path
     * @param array $vars
     * @return string|null
     */
    public function render($view_path, array $vars = null) {
        $this->assign($vars);
        $this->fetchVars();

        $isInclude = strpos($view_path, APP_PATH) === 0;
        $path      = Loader::getInstance()->filePath((!$isInclude ? $this->getScriptPath() : '') . $view_path, null);

        if (!file_exists($path)) {
            throw new LoadViewFailed($path . ': No such file or directory');
        }

        ob_start();
        ob_flush();
        require($path);
        $content = ob_get_contents();
        ob_clean();
        ob_end_flush();

        return $content;
    }

    /**
     * 当前视图引擎的模板文件基目录
     *
     * @param type $view_directory
     * @return \Ghalf\View
     */
    public function setScriptPath($view_directory) {
        $this->_script_path = (string)$view_directory;

        return $this;
    }

    /**
     * 当前视图引擎的模板文件基目录
     *
     * @return string
     */
    public function getScriptPath() {
        return $this->_script_path;
    }

    /**
     * 处理已登记的变量
     */
    protected function fetchVars(){
        if(!empty($this->_tpl_vars)){
            foreach ($this->_tpl_vars as $key => $value){
                $this->{$key} = $value;
            }
        }
    }
}
