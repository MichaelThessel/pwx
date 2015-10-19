<?php

namespace App\Entity;

/**
 * UserCredentials
 *
 * @Table(name="entity_credentials_table")
 * @Entity(repositoryClass="App\Entity\CredentialsRepository")
 */
class Credentials
{
    /**
     * @var string
     *
     * @Column(name="hash", type="string", length=10)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $hash;

    /**
     * @var string
     *
     * @Column(name="username", type="text", length=65535)
     */
    private $username;

    /**
     * @var string
     *
     * @Column(name="password", type="text", length=65535)
     */
    private $password;

    /**
     * @var string
     *
     * @Column(name="comment", type="text", length=65535)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @Column(name="expiresAt", type="datetime")
     */
    private $expiresAt;

    /**
     * Set hash
     *
     * @param string $hash
     * @return Credentials
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
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
     * @return Credentials
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
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
     * @return Credentials
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
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
     * @return Credentials
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
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
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     * @return Credentials
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime 
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
