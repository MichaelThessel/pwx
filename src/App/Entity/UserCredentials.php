<?php

namespace App\Entity;

use Doctrine\Mapping as ORM;
/**
 * UserCredentials
 *
 * @Table()
 * @Entity(repositoryClass="App\Entity\UserCredentialsRepository")
 */
class UserCredentials
{
    /**
     * @var integer
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="hash", type="string", length=255)
     */
    private $hash;

    /**
     * @var string
     */
    private $usernamePlainText;

    /**
     * @var string
     *
     * @Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     */
    private $passwordPlainText;

    /**
     * @var string
     *
     * @Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     */
    private $commentPlainText;

    /**
     * @var string
     *
     * @Column(name="comment", type="string", length=255)
     */
    private $comment;

    /**
     * @var string
     */
    private $emailPlainText;

    /**
     * @var string
     *
     * @Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var boolean
     *
     * @Column(name="expired", type="boolean")
     */
    private $expired;

    /**
     * @var \DateTime
     *
     * @Column(name="expiresAt", type="datetime")
     */
    private $expiresAt;

    /**
     * @var boolean
     *
     * @Column(name="oneTimeView", type="boolean")
     */
    private $oneTimeView;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return UserCredentials
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
     * Set usernamePlainText
     *
     * @param string $usernamePlainText
     * @return UserCredentials
     */
    public function setUsernamePlainText($usernamePlainText)
    {
        $this->usernamePlainText = $usernamePlainText;

        return $this;
    }

    /**
     * Get usernamePlainText
     *
     * @return string 
     */
    public function getUsernamePlainText()
    {
        return $this->usernamePlainText;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return UserCredentials
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
     * Set passwordPlainText
     *
     * @param string $passwordPlainText
     * @return UserCredentials
     */
    public function setPasswordPlainText($passwordPlainText)
    {
        $this->passwordPlainText = $passwordPlainText;

        return $this;
    }

    /**
     * Get passwordPlainText
     *
     * @return string 
     */
    public function getPasswordPlainText()
    {
        return $this->passwordPlainText;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return UserCredentials
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
     * Set commentPlainText
     *
     * @param string $commentPlainText
     * @return UserCredentials
     */
    public function setCommentPlainText($commentPlainText)
    {
        $this->commentPlainText = $commentPlainText;

        return $this;
    }

    /**
     * Get commentPlainText
     *
     * @return string 
     */
    public function getCommentPlainText()
    {
        return $this->commentPlainText;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return UserCredentials
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
     * Set emailPlainText
     *
     * @param string $emailPlainText
     * @return UserCredentials
     */
    public function setEmailPlainText($emailPlainText)
    {
        $this->emailPlainText = $emailPlainText;

        return $this;
    }

    /**
     * Get emailPlainText
     *
     * @return string 
     */
    public function getEmailPlainText()
    {
        return $this->emailPlainText;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return UserCredentials
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     * @return UserCredentials
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean 
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     * @return UserCredentials
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

    /**
     * Set oneTimeView
     *
     * @param boolean $oneTimeView
     * @return UserCredentials
     */
    public function setOneTimeView($oneTimeView)
    {
        $this->oneTimeView = $oneTimeView;

        return $this;
    }

    /**
     * Get oneTimeView
     *
     * @return boolean 
     */
    public function getOneTimeView()
    {
        return $this->oneTimeView;
    }
}
