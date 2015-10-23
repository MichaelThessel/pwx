<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Repository for Credentials entity
 */
class CredentialsRepository extends EntityRepository
{
    public function clean()
    {
        $qb = $this->createQueryBuilder('c');
        $qb ->delete()
            ->where($qb->expr()->lt('c.expires', '?1'))
            ->setParameter(1, time())
            ->getQuery()
            ->execute()
        ;
    }
}
