<?php

namespace Ghalf\Config;

class IniPHP {

    protected $_config  = [];
    protected $readonly = true;

    public function __construct($mixed, $environ = 'develop') {
        if (!file_exists($mixed)) {
            throw new \Ghalf\Exception('Unable to find config file ' . $mixed);
        }

        $parse  = new \Ghalf\Config\Parser\Parser($mixed);
        $config = $parse->getConfig();

        $this->_config = (isset($config[$environ])) ? $config[$environ] : [];
    }

    /**
     * 获取节点数据
     *
     * @param string $section
     * @return \Ghalf\Config\Ini|string
     */
    public function get($section) {
        $that     = clone $this;
        $config   = $that->_config;
        $sections = explode('.', $section);

        foreach ($sections as $key) {
            if (isset($config[$key])) {
                $item   = $config[$key];
                $config = $item;
            }
        }

        if (isset($item)) {
            if (is_string($item)) {
                unset($that);

                return $item;
            }

            $that->_config = $item;
        }

        return $that;
    }

    /**
     * 获取配置(数组格式)
     *
     * @return array
     */
    public function toArray() {
        return $this->_config;
    }

}
