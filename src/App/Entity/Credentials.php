<?php

namespace App\Entity;

use Crypt_RSA;

/**
 * UserCredentials
 *
 * @Table(name="credentials")
 * @Entity(repositoryClass="App\Entity\CredentialsRepository")
 * @HasLifecycleCallbacks()
 */
class Credentials
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
    protected $username;

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
     * Initialize hash
     * @PrePersist
     *
     * @return void
     */
    public function initHash()
    {
        $rsa = new Crypt_RSA();
        $key = $rsa->createKey();
        $this->hash = substr(md5($key['privatekey']), 0, 10);
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
     * Set username
     *
     * @param string $username
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
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
        if ($expires < 60 * 60 || $expires > 60 * 60 * 24 * 30) {
            $expires = 60 * 60;
        }

        $expires = time() + $expires;

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
}
