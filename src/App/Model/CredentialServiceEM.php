<?php

namespace App\Model;

use App\Entity\Credentials;
use Doctrine\ORM\EntityManager;

class CredentialServiceEM implements CredentialServiceInterface
{
    /**
     * @var CryptAESService
     */
    protected $aes;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param CryptAESService $aes
     */
    public function __construct(EntityManager $em, CryptAESService $aes)
    {
        $this->aes = $aes;
        $this->em = $em;
    }

    /**
     * Get credentials
     *
     * @param mixed $hash Hash to get credentials for
     * @return mixed Credential data
     */
    public function get($hash)
    {
        /**
         * @var Credentials $credentials
         */
        $credentials = $this->em
            ->getRepository('App\Entity\Credentials')
            ->findNotExpiredByHash($hash);


        if (!$credentials)
        {
            // Throw resourceNotFoundException
            return array(
                'userName' => null,
                'password' => null,
                'comment' => null,
                'expires' => null
            );
        }

        $this->aes->decrypt($credentials);

        return array(
            'userName' => $credentials->getUsername(),
            'password' => $credentials->getPassword(),
            'comment' => $credentials->getComment(),
            'expires' => $credentials->getExpires()
        );
    }

    /**
     * Save credentials
     *
     * @param string $userName User name
     * @param string $password Password
     * @param string $comment Comment
     * @param int $expires Expiry period
     * @return string Hash to identify saved credentials
     */
    public function save($userName, $password, $comment, $expires)
    {
        $credentials = new Credentials();
        $credentials->setUsername($userName);
        $credentials->setPassword($password);
        $credentials->setComment($comment);
        $credentials->setExpires($expires);

        $this->aes->encrypt($credentials);

        $this->em->persist($credentials);
        $this->em->flush();

        return $credentials->getHash();
    }

    /**
     * Delete credential
     *
     * @param string $hash Identifier for credentials to delete
     * @return string $hash
     */
    public function delete($hash)
    {
        /**
         * @var Credentials $credentials
         */
        $credentials = $this->em->getRepository('App\Entity\Credentials')
            ->findOneBy(array(
                'hash' => $hash
            ))
        ;

        if (!$credentials)
        {
            // Throw resourceNotFoundException
            // or
            // return
        }

        $this->em->remove($credentials);
        $this->em->flush();

        return $hash;
    }

    /**
     * Clean expired credentials from the db
     *
     * @return void
     */
    public function clean()
    {
        $this->em->getRepository('App\Entity\Credentials')
            ->clean()
        ;
    }
}
