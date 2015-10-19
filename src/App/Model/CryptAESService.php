<?php

namespace App\Model;

use App\Entity\Credentials;
use Crypt_AES;

class CryptAESService {

    /**
     * @var Crypt_AES
     */
    protected $cipher;

    /**
     * Constructor
     *
     * @param $config
     */
    public function __construct($config)
    {
        // Encrypt passwords with AES if secret is set
        if (isset($config['secret']) && !empty($config['secret'])) {
            $this->cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
            $this->cipher->setKey($config['secret']);
        }
    }

    /**
     * Get credentials
     *
     * @param Credentials $credentials
     * @return Credentials
     */
    public function decrypt(Credentials $credentials)
    {
        if ($this->cipher && $credentials) {
            $credentials['userName'] = $this->cipher->decrypt($credentials['userName']);
            $credentials['password'] = $this->cipher->decrypt($credentials['password']);
            $credentials['comment'] = $this->cipher->decrypt($credentials['comment']);
        }

        return $credentials;
    }

    /**
     * Save credentials
     *
     * @param Credentials $credentials
     * @return Credentials
     */
    public function encrypt(Credentials $credentials)
    {
        $expires = $credentials->getExpires();
        if ($expires < 60 * 60 || $expires > 60 * 60 * 24 * 30) {
            $expires = 60 * 60;
        }

        $expires = time() + $expires;

        // Use RSA 1024 bit private key to create random hash
        $rsa = new CryptRSAFactory();
        $hash = $rsa->createHash(10);

        if ($this->cipher) {
            $credentials->setUsername($this->cipher->encrypt($credentials->getUsername()));
            $credentials->setPassword($this->cipher->encrypt($credentials->getPassword()));
            $credentials->setComment($this->cipher->encrypt($credentials->getComment()));
            $credentials->setExpires($expires);
            $credentials->setHash($hash);
        } else {
            // Throw some exception?
        }

        return $credentials;
    }
}
