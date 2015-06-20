<?php

namespace Ghalf\Interfaces;

interface Router {
    public function route(\Ghalf\RequestAbstract $request);
}

