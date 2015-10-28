<?php

namespace App\Service;

use App\Entity\Credentials;
use App\Entity\CredentialsRepository;
use App\Factory\CredentialsFactory;
use Doctrine\ORM\EntityManager;

class CredentialsService extends AbstractCryptService
{
    /** @var EntityManager $em */
    protected $em;

    /** @var CredentialsFactory $credentialsFactory */
    protected $credentialsFactory;

    /** @var CredentialsRepository $credentialsRepository */
    protected $credentialsRepository;

    protected $cryptedProperties = array(
        'userName',
        'password',
        'comment',
    );

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param CredentialsFactory $credentialsFactory
     * @param CredentialsRepository $credentialsRepository
     * @param array $config
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
     * @param array $args Credentials to save
     * @return \App\Entity\Credentials
     */
    public function save($args)
    {
        // Limit expiry times
        if ($args['expires'] < 60 * 60 || $args['expires'] > 60 * 60 * 24 * 30) {
            $args['expires'] = 60 * 60;
        }
        $args['expires'] = time() + $args['expires'];

        $credentials = $this->credentialsFactory->getInstance();

        $credentials->setUserName($args['userName']);
        $credentials->setPassword($args['password']);
        $credentials->setComment($args['comment']);
        $credentials->setExpires($args['expires']);
        $credentials->setOneTimeView($args['oneTimeView']);

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
     * @return \App\Entity\Credentials
     */
    public function find($hash)
    {
        /** @var Credentials $credentials */
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
        $this->credentialsRepository->deleteExpired();
    }
}
