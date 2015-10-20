<?php

namespace App\Model;

use Doctrine\DBAL\Connection as DoctrineConnection;
use Crypt_AES;
use Crypt_RSA;

interface CredentialServiceInterface {

    public function get($hash);
    public function save($userName, $password, $comment, $expires);
    public function delete($hash);
    public function clean();

}
