<?php

namespace App\Service;

use App\Model\CryptAES;

/**
 * Handles encryption of properties. Properties specified in $cryptedProperties
 */
abstract class AbstractCryptService extends CryptAES
{
    protected $cryptedProperties = array();

    /**
     * Encrypt properties that have been flagged in $cryptedProperties
     *
     * @return void
     */
    public function encryptProperties($item)
    {
        if (!is_null($item->isEncrypted()) && $item->isEncrypted()) return;

        $this->cryptProperties('encrypt', $item);
        $item->setIsEncrypted(true);
    }

    /**
     * Decrypt properties that have been flagged in $cryptedProperties
     *
     * @return void
     */
    public function decryptProperties($item)
    {
        if (!is_null($item->isEncrypted()) && !$item->isEncrypted()) return;

        $this->cryptProperties('decrypt', $item);
        $item->setIsEncrypted(false);
    }

    /**
     * En/DecryptProperties
     *
     * @param mixed $mode encrypt/decrypt
     * @return void
     */
    protected function cryptProperties($mode, $item)
    {
        if (!in_array($mode, array('encrypt', 'decrypt'))) {
            throw new \InvalidArgumentException('Invalid mode specified');
        }

        if (!isset($this->cryptedProperties) || empty($this->cryptedProperties)) return;

        foreach ($this->cryptedProperties as $cryptedProperty) {
            $get = 'get' . ucfirst($cryptedProperty);
            $set = 'set' . ucfirst($cryptedProperty);
            $item->$set($this->$mode($item->$get()));
        }
    }
}
