<?php

namespace App\Model;

use Crypt_RSA;

class CryptRSAService {

    /**
     * @param $length
     *
     * @return string
     */
    public function createHash($length = 10)
    {
        $rsa = new Crypt_RSA();
        $key = $rsa->createKey();
        return substr(md5($key['privatekey']), 0, $length);
    }
}
