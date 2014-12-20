<?php

/**
 * 此方式利用了parse_ini_file来解析ini配置
 */

namespace Ghalf\Config;

final class Ini {

    protected $_config  = [];
    protected $readonly = true;

    public function __construct($mixed, $environ = 'develop') {
        if (!file_exists($mixed)) {
            throw new \Ghalf\Exception('Unable to find config file . ' . $mixed);
        }

        $this->_config = parse_ini_file($mixed, true);
        $this->_parse($environ);
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

    /**
     * 解析节点为数据数组
     *
     * @param string $environ
     * @return boolean
     */
    private function _parse($environ) {
        $config = $this->_config;
        $allow  = array('common' => 1, $environ => 1);

        foreach ($config as $section => $data) {
            unset($this->_config[$section]);

            $parent = null;
            $name   = trim(strtolower($section));
            $index  = strpos($name, ':');

            if ($index > 0) {
                $parent = trim(substr($name, $index + 1));
                $name   = trim(substr($name, 0, $index));
            }

            if (!isset($allow[$name])) {
                continue;
            }

            $this->_toArray($data, $name, $parent);
        }

        $this->_config = isset($this->_config[$environ]) ? $this->_config[$environ] : [];

        return true;
    }

    /**
     * 节点数据转成数组
     *
     * @param array $data
     * @param string $section
     * @param string $copy
     * @return boolean
     */
    private function _toArray(array $data, $section, $copy = null) {
        if (!isset($this->_config[$section])) {
            $this->_config[$section] = ($copy && isset($this->_config[$copy]) ? $this->_config[$copy] : []);
        }

        foreach ($data as $key => $value) {
            $keyArr = explode('.', $key);

            if (!is_array($value)) {
                is_string($value) && $this->_push($section, $keyArr, $value);
                continue;
            }

            foreach ($value as $k => $v) {
                $keyArr[] = $k;

                $this->_push($section, $keyArr, $v);
            }
        }

        return true;
    }

    /**
     * 数据压入节点
     *
     * @param string $section
     * @param array $keyArr
     * @param string $value
     * @return true
     */
    private function _push($section, array $keyArr, $value) {
        $json = '{"' . implode('":{"', $keyArr) . '":"' . addslashes(trim((string)$value)) . '"';
        $json .= str_repeat('}', count($keyArr));

        $this->_config[$section] = array_replace_recursive($this->_config[$section], \json_decode($json, true));

        return true;
    }

}
