<?php

namespace Ghalf\Exception;

use \Ghalf\Consts;

class TypeError extends \Ghalf\Exception {

    protected $code = Consts::ERR_TYPE_ERROR;

}