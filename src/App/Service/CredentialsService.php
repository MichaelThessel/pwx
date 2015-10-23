<?php

namespace App\Service;

use App\Entity\CredentialsRepository;
use App\Factory\CredentialsFactory;
#use App\Service\AbstractCryptService;
use Doctrine\ORM\EntityManager;

class CredentialsService extends AbstractCryptService
{
    protected $em;

    protected $credentialsFactory;

    protected $credentialsRepository;

    protected $cryptedProperties = array(
        'username',
        'password',
        'comment',
    );

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param CredentialsFactory $credentialsFactory
     * @param CredentialsRepository $credentialsRepository
     */
    public function __construct(
        EntityManager $em,
        CredentialsFactory $credentialsFactory,
        CredentialsRepository $credentialsRepository,
        $config
    )
    {
        $this->em = $em;
        $this->credentialsFactory = $credentialsFactory;
        $this->credentialsRepository = $credentialsRepository;
        $this->config = $config;
    }

    /**
     * Save credentials
     *
     * @param array $credentials Credentials to save
     * @return Credentials
     */
    public function save($args)
    {
        $credentials = $this->credentialsFactory->getInstance();

        $credentials->setUsername($args['userName']);
        $credentials->setPassword($args['password']);
        $credentials->setComment($args['comment']);
        $credentials->setExpires($args['expires']);

        $this->encryptProperties($credentials);

        $this->em->persist($credentials);
        $this->em->flush();

        $this->decryptProperties($credentials);

        return $credentials;
    }

    /**
     * Find credentials by hash
     *
     * @param string $hash Hash to find
     * @return Credentials
     */
    public function find($hash)
    {
        $credentials = $this->credentialsRepository->find($hash);
        if ($credentials) $this->decryptProperties($credentials);

        return $credentials;
    }

    /**
     * Delete credentials by hash
     *
     * @param string $hash Hash to delete
     * @return void
     */
    public function delete($hash)
    {
        if ($hash) {
            $credentials = $this->credentialsRepository->find($hash);
            if ($credentials) {
                $this->em->remove($credentials);
                $this->em->flush();
            }
        }
    }

    /**
     * Delete expired credentials
     *
     * @return void
     */
    public function deleteExpired()
    {
        $this->credentialsRepository->clean();
    }
}
