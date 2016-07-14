<?php

namespace App\Model;
use phpseclib\Crypt\AES;


/**
 * Handles AES encryption
 */
class CryptAES {

    /** @var AES */
    protected $cipher;

    /**
     * Set Cipher
     */
    public function setCipher()
    {
        // Encrypt passwords with AES if secret is set
        if (isset($this->config['secret']) || $this->config['secret']) {
            $this->cipher = new AES(AES::MODE_ECB);
            $this->cipher->setKey($this->config['secret']);
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
