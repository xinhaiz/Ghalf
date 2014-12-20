<?php

namespace Ghalf;

use Ghalf\Exception\LoadFailed;

final class Loader {

    protected static $_instance = null;

    /**
     * 已注册本地类库命名空间
     *
     * @var string
     */
    protected $_local_namespace = null;

    /**
     * 全局配置
     *
     * @var GlobalConfig
     */
    protected $_global = null;

    /**
     * 前缀后缀组
     *
     * @var array
     */
    protected $_suffixPreix = array('Controller' => 10, 'Model' => 5, 'Plugin' => 6);

    public function __construct() {
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * 单例
     *
     * ＠return Ghalf\Instance
     */
    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 自动载入
     *
     * @param strig $className
     */
    public function autoload($className) {
        if (strpos($className, 'Ghalf\\') !== 0) {
            $global = $this->_global;

            if (!$global instanceof GlobalConfig) {
                $global = Register::get(Consts::GC);

                $this->_global = $global;
            }

            $className = $global->getName_suffix() === false ? $this->parsePrefix($className) : $this->parseSuffix($className);

            if ($className === null) {
                return false;
            }
        }

        return $this->import($className);
    }

    /**
     * 引入文件
     *
     * @staticvar array $_imported
     * @param string $filename
     * @return boolean
     */
    public function import($filename) {
        static $_imported = [];

        $filePath = $this->filePath($filename);
        $filekey  = str_replace(DS, '_', $filePath);

        if (!isset($_imported[$filekey])) {
            if (!file_exists($filePath)) {
                throw new LoadFailed($filename . ': No such file or directory', $filePath);
            }

            require($filePath);
            $_imported[$filekey] = 1;

            return true;
        }

        return (isset($_imported[$filekey]) && $_imported[$filekey] === 1) ? true : false;
    }

    /**
     * 文件路径
     *
     * @param string $file
     * @param string $ext
     * @return string
     */
    public function filePath($file, $ext = '.php') {
        $prevPath = strpos($file, APP_PATH) === 0 ? '' : APP_PATH . DS;

        return $prevPath . str_replace('\\', DS, $file) . $ext;
    }

    /**
     * 注册本地类库命名空间
     *
     * @param string|array $localNamespace
     * @return Loader
     */
    public function registerLocalNamespace($localNamespace) {
        $name = (is_string($localNamespace)) ? array($localNamespace) : $localNamespace;

        $this->_local_namespace = sprintf(':%s:', implode(':', $name));

        return $this;
    }

    /**
     * 判断一个类, 是否是本地类.
     *
     * @param string $className
     * @return boolean
     */
    public function isLocalName($className) {
        $classArr = explode('\\', $className);

        return (bool)(strpos($this->_local_namespace, ':' . array_shift($classArr) . ':') !== false);
    }

    /**
     * 本地类
     *
     * @return string
     */
    public function getLocalNamespace() {
        return $this->_local_namespace;
    }

    /**
     * 清除已注册本地类
     *
     * @return Loader
     */
    public function clearLocalNamespace() {
        return (bool)($this->_local_namespace = null);
    }

    /**
     * 解析类名（后缀式）
     *
     * @param string $classTmp
     * @return string
     */
    private function parseSuffix($classTmp) {
        $global    = $this->_global;
        $suffixs   = $this->_suffixPreix;
        $separ     = $global->getName_separator();
        $dir       = $global->getDirectory();
        $sepLen    = strlen($separ);
        $classArr  = explode('\\', trim($classTmp, '\\'));
        $className = array_pop($classArr);
        $classPre  = empty($classArr) ? '' : DS . trim(implode(DS, $classArr));
        $classLen  = strlen($className);

        foreach ($suffixs as $suffix => $len) {
            $mixLen   = $len + $sepLen;
            $index    = strpos($className, $separ . $suffix);
            $funcName = 'get' . $suffix . 'Dir';

            if ($index > 0 && $index === $classLen - $mixLen && method_exists($this, $funcName)) {
                $className = substr($className, 0, $index);
                $classMark = call_user_func_array(array($this, $funcName), array($className, $classPre, $dir));
                break;
            }
        }

        if (!isset($classMark)) {
            $classMark = $dir . 'library' . $classPre . DS . $className;
        }

        return $classMark;
    }

    /**
     * 解析类名（前缀式）
     *
     * @param string $classTmp
     * @return string
     */
    private function parsePrefix($classTmp) {
        $global    = $this->_global;
        $suffixs   = $this->_suffixPreix;
        $separ     = $global->getName_separator();
        $dir       = $global->getDirectory();
        $classArr  = explode('\\', trim($classTmp, '\\'));
        $className = array_pop($classArr);
        $classPre  = empty($classArr) ? '' : DS . trim(implode(DS, $classArr));

        foreach ($suffixs as $prefix => $len) {
            $funcName  = 'get' . $prefix . 'Dir';
            $prefixStr = $prefix . $separ;

            if (strpos($className, $prefixStr) === 0 && method_exists($this, $funcName)) {
                $className = str_replace($prefixStr, '', $className);
                $classMark = call_user_func_array(array($this, $funcName), array($className, $classPre, $dir));
                break;
            }
        }

        if (!isset($classMark)) {
            $classMark = $dir . 'library' . $classPre . DS . $className;
        }

        return $classMark;
    }

    /**
     * 获取控制器目录片段
     *
     * @param string $className
     * @param string $classPre
     * @param string $dir
     * @return string
     */
    private function getControllerDir($className, $classPre, $dir) {
        $filePath   = $dir . 'controllers' . $classPre . DS . $className;
        $moduleName = $this->getModuleName();

        if (!file_exists($filePath . $this->_global->getExt()) && !empty($moduleName)) {
            $filePath = $dir . 'modules' . DS . $moduleName . DS . 'controllers' . $classPre . DS . $className;
        }

        return $filePath;
    }

    /**
     * 获取模型目录片段
     *
     * @param string $className
     * @param string $classPre
     * @param string $dir
     * @return string
     */
    private function getModelDir($className, $classPre, $dir) {
        $filePath   = $dir . 'models' . $classPre . DS . $className;
        $moduleName = $this->getModuleName();

        if (!file_exists($filePath . $this->_global->getExt()) && !empty($moduleName)) {
            $filePath = $dir . 'modules' . DS . $moduleName . DS . 'models' . $classPre . DS . $className;
        }

        return $filePath;
    }

    /**
     * 获取插件目录片段
     *
     * @param string $className
     * @param string $classPre
     * @param string $dir
     * @return string
     */
    private function getPluginDir($className, $classPre, $dir) {
        return $dir . 'plugins' . $classPre . DS . $className;
    }

    /**
     * 当前模块
     *
     * @return string
     */
    private function getModuleName() {
        $moduleName = Dispatcher::getInstance()->getRequest()->getModuleName();

        return (!empty($moduleName) ? trim($moduleName) : '');
    }

}
