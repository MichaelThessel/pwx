<?php

namespace App\Model;

/**
 * Class RedirectService
 * @package App\Model
 */
class ForceSSLService {

    protected $forceSSL;

    /**
     * Constructor
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->forceSSL = $config['forceSSL'];
    }

    public function forceSSLIfSet()
    {
        if ($this->forceSSL)
        {
            $this->redirectToHTTPS();
        }
    }

    /**
     * Redirect
     */
    protected function redirectToHTTPS()
    {
        if($_SERVER["HTTPS"] != "on") {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
            exit();
        }
    }
}
