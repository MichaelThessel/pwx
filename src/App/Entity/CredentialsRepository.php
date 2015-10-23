<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Repository for Credentials entity
 */
class CredentialsRepository extends EntityRepository
{
    /**
     * Delete expired entries
     *
     * @return void
     */
    public function deleteExpired()
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
