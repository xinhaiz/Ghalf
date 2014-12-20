<?php

namespace Ghalf\Interfaces;

interface View {
    public function render($view_path, array $vars = null);
    public function display($view_path, array $vars = null);
    public function assign($name, $value);
    public function setScriptPath($view_directory);
    public function getScriptPath();
}