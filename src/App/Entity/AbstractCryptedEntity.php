<?php

namespace App\Entity;

use App\Model\CryptAES;

/**
 * Handles encryption of properties. Properties specified in $cryptedProperties
 * will be automaitically de/encrypted when stored and retrieved from the
 * database
 *
 * @HasLifecycleCallbacks()
 */
abstract class AbstractCryptedEntity extends CryptAES
{
    protected $cryptedProperties = array();

    /**
     * Encrypt properties that have been flagged in $cryptedProperties
     *
     * @PreUpdate
     * @PrePersist
     *
     * @return void
     */
    public function encryptProperties()
    {
        $this->cryptProperties('encrypt');
    }

    /**
     * Decrypt properties that have been flagged in $cryptedProperties
     * @PostLoad
     *
     * @return void
     */
    public function decryptProperties()
    {
        $this->cryptProperties('decrypt');
    }

    /**
     * En/DecryptProperties
     *
     * @param mixed $mode encrypt/decrypt
     * @return void
     */
    protected function cryptProperties($mode)
    {
        if (!in_array($mode, array('encrypt', 'decrypt'))) {
            throw new \InvalidArgumentException('Invalid mode specified');
        }

        if (!isset($this->cryptedProperties) || empty($this->cryptedProperties)) return;

        foreach ($this->cryptedProperties as $cryptedProperty) {
            $this->$cryptedProperty = $this->$mode($this->$cryptedProperty);
        }
    }
}
