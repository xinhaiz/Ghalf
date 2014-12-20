<?php

namespace Ghalf\Exception;

use \Ghalf\Consts;

class StartupError extends \Ghalf\Exception {

    protected $code = Consts::ERR_STARTUP_FAILED;

}