<?php

namespace Ghalf\Exception;

use \Ghalf\Consts;

class LoadFailed extends \Ghalf\Exception {

    protected $code = Consts::ERR_AUTOLOAD_FAILED;

}