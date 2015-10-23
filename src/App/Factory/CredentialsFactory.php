<?php

namespace App\Factory;

use App\Entity\Credentials;
use Crypt_RSA;

class CredentialsFactory {

    /**
     * Get Credentials instance
     *
     * @return Credentials Initialized credentials instance
     */
    public function getInstance() {
        $credentials = new Credentials();

        $rsa = new Crypt_RSA();
        $key = $rsa->createKey();
        $credentials->setHash(substr(md5($key['privatekey']), 0, 10));

        return $credentials;
    }
}
