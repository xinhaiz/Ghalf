<?php

namespace Ghalf\Exception;

use \Ghalf\Consts;

class LoadViewFailed extends \Ghalf\Exception {

    protected $code = Consts::ERR_VIEW_404;

}
