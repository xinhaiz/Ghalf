<?php

namespace Ghalf\Interfaces;

interface Router {
    public function route(\Ghalf\RequestAbstract $request);
    public function assemble(array $info, array $query = null);
}

