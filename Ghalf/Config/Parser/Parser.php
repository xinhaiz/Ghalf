<?php

namespace Ghalf\Config\Parser;

use \Ghalf\Config\Buffer\Char as Char;
use \Ghalf\Config\Parser\Ruler as Ruler;

class Parser {

    protected $_config = null;
    protected $_mixed  = null;

    public function __construct($mixed) {
        $this->_mixed = $mixed;
        $this->run();
    }

    /**
     * 获取解析后的数据
     *
     * @return array
     */
    public function getConfig() {
        return $this->_config;
    }

    /**
     * 执行解析
     *
     * @return boolean
     */
    protected function run() {
        $contents = $this->getContents();

        if (!empty($contents)) {
            $ruler = Ruler::getInstance();

            $data = $ruler->run(new Char($contents . "\n")); // 增加 \n 避免意外, 类 \0

            $this->_config = ($data ? $data : []);
        }

        return true;
    }

    /**
     * 读取配置文件内容
     *
     * @return string
     */
    protected function getContents() {
        $mixed = $this->_mixed;

        return (file_exists($mixed) ? trim(file_get_contents($mixed)) : (string)$mixed);
    }

}
