<?php

namespace Ghalf\Exception;

use \Ghalf\Consts;

class LoadModuleFailed extends \Ghalf\Exception {

    protected $code = Consts::ERR_MODULE_404;

}
