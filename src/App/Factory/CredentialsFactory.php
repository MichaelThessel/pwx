<?php

namespace App\Factory;

use App\Entity\Credentials;
use phpseclib\Crypt\RSA;

class CredentialsFactory {

    /**
     * Get Credentials instance
     *
     * @return Credentials Initialized credentials instance
     */
    public function getInstance() {
        $credentials = new Credentials();

        $rsa = new RSA();
        $key = $rsa->createKey();
        $credentials->setHash(substr(md5($key['privatekey']), 0, 10));

        return $credentials;
    }
}
