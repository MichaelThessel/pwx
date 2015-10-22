<?php

namespace App\Model;

use App\Entity\Credentials;

class CredentialsFactory {

    /**
     * Constructor
     *
     * @param $userName
     * @param $password
     * @param $comment
     * @param $period
     *
     * @return Credentials
     */
    public static function createCredentials($userName = "", $password = "", $comment = "", $period = 0) {

        $credentials = new Credentials();

        $credentials->setUsername($userName);
        $credentials->setPassword($password);
        $credentials->setComment($comment);

        if ($period < 60 * 60 || $period > 60 * 60 * 24 * 30) {
            $period = 60 * 60;
        }

        $credentials->setExpires(time() + $period);

        return $credentials;
    }
}
