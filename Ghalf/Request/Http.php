<?php

namespace Ghalf\Request;

final class Http extends \Ghalf\RequestAbstract {

    const SCHEME_HTTP  = 'http';
    const SCHEME_HTTPS = 'https';
    const REQUEST_URL  = '%s://%s';

    public function __construct() {
        parent::__construct();

        $args = func_get_args();

        $request_uri = (isset($args[0])) ? $args[0] : (string)$this->getServer('REQUEST_URI');
        $base_uri    = (isset($args[1])) ? $args[1] : null;

        if (!empty($request_uri)) {
            $index = strpos($request_uri, '?');

            if($index > 0){
                $request_uri = substr($request_uri, 0, $index);
            }

            $request_uri  = str_replace('//', '', $request_uri);
            $globalConfig = \Ghalf\Register::get(\Ghalf\Consts::GC);
            
            if($globalConfig instanceof \Ghalf\GlobalConfig) {
                $default = $globalConfig->getDefaultController() . $globalConfig->getExt();
            } else {
                $default = 'index.php';
            }
            
            if(strcasecmp($request_uri, '/' . $default) === 0) {
                $request_uri = '/';
            }

            $this->setRequestUri(trim(trim($request_uri, '/')));
        }

        if (empty($base_uri)) {
            $host = $this->getServer('HTTP_HOST');

            if (!empty($host)) {
                $ssl = ('on' === $this->getServer(self::SCHEME_HTTPS)) ? true : false;
                
                $base_uri = sprintf(
                     self::REQUEST_URL,
                     ($ssl === true ? self::SCHEME_HTTPS : self::SCHEME_HTTP),
                     $host
                );
            }
        }

        if (!empty($base_uri)) {
            $this->setBaseUri($base_uri);
        }
    }

}
