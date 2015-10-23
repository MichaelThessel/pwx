<?php

namespace App\Service;

use App\Entity\CredentialsRepository;
use App\Factory\CredentialsFactory;
use Doctrine\ORM\EntityManager;

class CredentialsService
{
    protected $em;

    protected $credentialsFactory;

    protected $credentialsRepository;

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
        CredentialsRepository $credentialsRepository
    )
    {
        $this->em = $em;
        $this->credentialsFactory = $credentialsFactory;
        $this->credentialsRepository = $credentialsRepository;

    }

    /**
     * Save credentials
     *
     * @param array $credentials Credentials to save
     * @return Credentials
     */
    public function save($credentials)
    {
        $instance = $this->credentialsFactory->getInstance();

        $instance->setUsername($credentials['userName']);
        $instance->setPassword($credentials['password']);
        $instance->setComment($credentials['comment']);
        $instance->setExpires($credentials['period']);

        $this->em->persist($instance);
        $this->em->flush();

        return $instance;
    }

    /**
     * Find credentials by hash
     *
     * @param string $hash Hash to find
     * @return Credentials
     */
    public function find($hash)
    {
        return $this->credentialsRepository->find($hash);
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
