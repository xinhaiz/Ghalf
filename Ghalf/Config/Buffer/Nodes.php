<?php

namespace Ghalf\Config\Buffer;

final class Nodes {
    use \Ghalf\Config\Depend\Instance;

    protected $_main_node   = null;
    protected $_main_name   = null;
    protected $_child_key   = [];
    protected $_copy_node   = [];
    protected $_final_node  = [];

    /**
     * 全局节点数据
     *
     * @return array
     */
    public function pull() {
        $copyNode = $this->_copy_node;

        if (!empty($copyNode)) {
            foreach ($copyNode as $source => $to) {
                if (isset($this->_main_node[$source])) {
                    $mainNode   = $this->_main_node[$to];
                    $extendNode = $this->_main_node[$source];

                    $this->_main_node[$to] = array_merge_recursive($mainNode, $extendNode);
                }
            }
        }

        return $this->_main_node;
    }

    /**
     * 重置
     */
    public function reset() {
        $this->_main_node  = [];
        $this->_child_key  = [];
        $this->_copy_node  = [];
        $this->_final_node = [];
    }

    /**
     * 创建主节点
     *
     * @param string $name
     */
    public function buildMain($name) {
        if (!empty($name)){
            if(!isset($this->_main_node[$name])) {
                $this->_main_node[$name] = [];
            }

            $this->_main_name = (string)$name;
        }
    }

    /**
     * 创建子节点
     *
     * @param string $name
     */
    public function buildChild($name = null) {
        if (empty($name)) {
            $name = $this->buildIndex();
        }

        $this->_child_key[] = $name;
    }

    /**
     * $value压入节点末节, 数据并压入主节点
     *
     * @param string $value
     */
    public function push($value) {
        $set  = '{"' . implode('":{"', $this->_child_key) . '":"' . (string) (trim($value)) . '"';
        $set .= str_repeat('}', count($this->_child_key));
        $this->_child_key = [];

        if(isset($this->_main_node[$this->_main_name])){
            $this->_main_node[$this->_main_name] = array_replace_recursive(
                    $this->_main_node[$this->_main_name], (array)json_decode($set, true)
            );
        }
    }

    /**
     * 复制主节点$name数据到当前主节点， 合并
     * 这里仅仅标记， 不进行实际操作，
     * pull 数据时将进行处理
     *
     * @param string $name
     */
    public function copy($name) {
        if(!isset($this->_final_node[$name])){
            $this->_copy_node[$name] = $this->_main_name;
        }
    }

    /**
     * 当前主节点
     *
     * @return string
     */
    public function getMain(){
        return (string)$this->_main_name;
    }

    /**
     * 添加 final 节点名称
     *
     * @param string $name
     */
    public function addFinal($name){
        if(!isset($this->_final_node[$name])){
            $this->_final_node[$name] = 1;
        }
    }

    /**
     * 创建全局数字索引
     *
     * return number
     */
    public function buildIndex() {
        static $_index = [];

        $key = $this->_main_name . '.' . implode('.', $this->_child_key);

        $_index[$key] = (isset($_index[$key])) ? $_index[$key] + 1 : 0;

        return $_index[$key];
    }

}
