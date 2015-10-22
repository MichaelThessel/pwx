<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\Credentials;
use App\Model\CryptAESService;

class AESCrypter
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Credentials)
        {
            $cryptAESService = new CryptAESService($this->config);
            $cryptAESService->encrypt($entity);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Credentials)
        {
            $cryptAESService = new CryptAESService($this->config);
            $cryptAESService->decrypt($entity);
        }
    }
}