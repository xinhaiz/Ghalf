<?php

namespace Ghalf\Exception;

use \Ghalf\Consts;

class DispatchFailed extends \Ghalf\Exception {

    protected $code = Consts::ERR_DISPATCH_FAILED;

}