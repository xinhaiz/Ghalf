<?php

namespace Ghalf;

use \Ghalf\Register;
use \Ghalf\Consts;

class Exception extends \Exception {

    protected $code = 0;
    protected $message = null;
    private $_previous = null;

    public function __construct($message, $code = 0, $previous = null) {
        $this->message = (string)$message;

        if($code > 0){
            $this->code = (int)$code;
        }

        if($previous instanceof \Exception){
            $this->_previous = $previous;
        }

        if(Register::get(Consts::GC)->getThrowException() === true){
            parent::__construct($message, $this->code, $this->_previous);
        }else{
            trigger_error($message, E_RECOVERABLE_ERROR);
        }
    }
}
