<?php

namespace App\Factory;

use App\Entity\Credentials;

class CredentialsFactory {

    /**
     * Get Credentials instance
     *
     * @return Credentials Initialized credentials instance
     */
    public function getInstance() {
        return new Credentials();
    }
}
