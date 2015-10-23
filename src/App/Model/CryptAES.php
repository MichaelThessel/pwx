<?php

namespace App\Model;

use Crypt_AES;

/**
 * Handles AES encryption
 */
class CryptAES {

    protected $cipher;

    /**
     * Set Cipher
     */
    public function setCipher()
    {
        // Encrypt passwords with AES if secret is set
        // TODO: ideally the secret wouldn't be passed in a constant. I haven't
        // found a acceptable way of passing the secret otherwise
        if (defined('APP_SECRET')) {
            $this->cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
            $this->cipher->setKey(APP_SECRET);
            return true;
        }

        return false;
    }

    /**
     * Decrypt value
     *
     * @param string $crypt Value to decrypt
     * @return string Decrypted value
     */
    public function decrypt($crypt)
    {
        if (!$this->cipher && !$this->setCipher()) return $crypt;

        return $this->cipher->decrypt($crypt);
    }

    /**
     * Encrypt value
     *
     * @param string $crypt Value to encrypt
     * @return string Encrypted value
     */
    public function encrypt($crypt)
    {
        if (!$this->cipher && !$this->setCipher()) return $crypt;

        return $this->cipher->encrypt($crypt);
    }
}
