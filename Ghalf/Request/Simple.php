<?php

namespace Ghalf\Request;

final class Simple extends \Ghalf\RequestAbstract {

    protected function init(){
        $args   = func_get_args();
        $num    = func_num_args();
        $params = [];

        if($num > 4 || $num < 1){
            return false;
        }

        $last = array_pop($args);

        if(is_array($last)){
            $params = $last;
            $a = array_pop($args);
        }else{
            $a = $last;
        }

        if($num > 1){
            if($num > 2){
                $c = array_pop($args);
                $m = array_pop($args);
            }else{
                $c = array_pop($args);
            }
        }

        if(!empty($m)){
            $this->setModuleName($m);
        }

        if(!empty($c)){
            $this->setControllerName($c);
        }

        $this->setActionName($a);

        if(!empty($params)){
            foreach ($params as $key => $param){
                $this->setParam($key, $param);
            }
        }
    }
    
}