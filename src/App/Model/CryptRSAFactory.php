<?php

namespace App\Model;

use Crypt_RSA;

class CryptRSAFactory {

    /**
     * @param $length
     *
     * @return string
     */
    public function createHash($length)
    {
        $rsa = new Crypt_RSA();
        $key = $rsa->createKey();
        return substr(md5($key['privatekey']), 0, $length);
    }
}
