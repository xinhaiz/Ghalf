<?php

namespace Ghalf\Exception;

use \Ghalf\Consts;

class LoadControllerFailed extends \Ghalf\Exception {

    protected $code = Consts::ERR_CONTROLLER_404;

}
