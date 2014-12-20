<?php

namespace Ghalf\Exception;

use \Ghalf\Consts;

class RouterFailed extends \Ghalf\Exception {

    protected $code = Consts::ERR_ROUTE_FAILED;

}

