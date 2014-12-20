<?php

namespace Ghalf\Config\Struct;

final class Mark {
    use \Ghalf\Config\Depend\Instance;

    // const ** 0x0** 此项不可修改
    const AA = 0x0FA; // [
    const AB = 0x0FB; // ]
    const AC = 0x0FC; // ;
    const AD = 0x0FD; // =
    const AE = 0x0FE; // "
    const AF = 0x0FF; // '
    const BA = 0x0EA; // \n
    const BB = 0x0EB; // .
    const BC = 0x0EC; // :
    const BD = 0x0ED; // \r \t " "

    private $_mark = array(
        '['      => self::AA,
        ']'      => self::AB,
        ';'      => self::AC,
        '='      => self::AD,
        '"'      => self::AE,
        '\''     => self::AF,
        "\n"     => self::BA,
        '.'      => self::BB,
        ':'      => self::BC,
        "\r"     => self::BD,
        " "      => self::BD,
        "\t"     => self::BD
    );

    private $_fun  = array(
        self::AA => 'left_bracket', // [
        self::AB => 'right_bracket',// ]
        self::AC => 'semicolon',    // ;
        self::AD => 'equal',        // =
        self::AE => 'double_quot',  // "
        self::AF => 'single_quot',  // '
        self::BA => 'newline',      // \n
        self::BB => 'dot',          // .
        self::BC => 'extend',       // :
        self::BD => 'space_tab'     // \r \t " "
    );

    /**
     * 标识码
     *
     * @param string $char
     * @return int
     */
    public function getMark($char) {
        return (isset($this->_mark[$char]) ? $this->_mark[$char] : 0);
    }

    /**
     * 标识方法
     *
     * @param string $code
     * @return array
     */
    public function getFun($code) {
        return (($code > 0 && isset($this->_fun[$code])) ? $this->_fun[$code] : false);
    }

}
