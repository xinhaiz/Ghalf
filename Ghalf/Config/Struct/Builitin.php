<?php

namespace Ghalf\Config\Struct;

final class Builitin {
    use \Ghalf\Config\Depend\Instance;

    // const ** 0x1** 此项不可修改
    const AA = 0x1FA; // import 导入其它ini
    const AB = 0x1FB; // final  节点禁止extend

    private $_builitin = array(
        'import' => self::AA,
        'final'  => self::AB
    );

    // 关键字参数需求
    private $_params  = array(
        0x1FA => 1,
        0x1FB => 1
    );

    // 关键字参数类型, 格式 *|** |* 表示非必须
    // s 字符串，事实上只有 s,
    private $_type = array(
        0x1FA => 's',
        0x1FB => 's'
    );

    /**
     * 关键字内置码
     *
     * 0 不是关键字码
     * @param string $code
     * @return int
     */
    public function get($code){
        return (isset($this->_builitin[$code]) ? $this->_builitin[$code] : 0);
    }

    /**
     * 获取参数需求数
     *
     * @param int $code
     * @return int
     */
    public function getParam($code){
        return (isset($this->_params[$code]) ? $this->_params[$code] : 0);
    }

    /**
     * 关键字参数类型
     *
     * @param int $code
     * @return array|string|false
     */
    public function getType($code){
        return (isset($this->_type[$code]) ? $this->_type[$code] : false);
    }
}

