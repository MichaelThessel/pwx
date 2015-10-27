<?php

namespace App\Entity;

/**
 * UserCredentials
 *
 * @Table(name="credentials")
 * @Entity(repositoryClass="App\Entity\CredentialsRepository")
 */
class Credentials implements \JsonSerializable
{
    /**
     * @Column(name="hash", type="string", length=10, options={"default" = ""})
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $hash;

    /**
     * @Column(name="username", type="text", length=65535)
     */
    protected $userName;

    /**
     * @Column(name="password", type="text", length=65535)
     */
    protected $password;

    /**
     * @Column(name="comment", type="text", length=65535)
     */
    protected $comment;

    /**
     * @Column(name="expires", type="integer")
     */
    protected $expires;

    protected $isEncrypted;

    /**
     * Set hash
     *
     * @param string $hash
     * @return void
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set userName
     *
     * @param string $userName
     * @return void
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set expires
     *
     * @param integer $expires
     * @return void
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * Get expires
     *
     * @return integer
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Get encryption state
     *
     * @return bool Encrypion state
     */
    public function isEncrypted()
    {
        return $this->isEncrypted;
    }

    /**
     * Set encryption state
     *
     * @param bool $isEncrypted Whether or not the entity is encrypted
     * @return void
     */
    public function setEncrypted($isEncrypted)
    {
        $this->isEncrypted = $isEncrypted;
    }

    public function jsonSerialize()
    {
        $data = array(
            'hash' => $this->hash,
            'userName' => $this->userName,
            'password' => $this->password,
            'comment' => $this->comment
        );
        return $data;
    }
}
