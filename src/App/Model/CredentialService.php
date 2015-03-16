<?php

namespace App\Model;

use Doctrine\DBAL\Connection as DoctrineConnection;

class CredentialService {

    protected $db;
    protected $table = 'credentials';

    /**
     * Constructor
     *
     * @param DoctrineConnection $db
     */
    public function __construct(DoctrineConnection $db)
    {
        $this->db = $db;
    }

    /**
     * Get credentials
     *
     * @param mixed $hash Hash to get credentials for
     * @return mixed Credential data
     */
    public function get($hash)
    {
        $qb = $this->db->createQueryBuilder();
        return $qb->select('userName', 'password', 'comment', 'expires')
            ->from($this->table)
            ->where($qb->expr()->andX(
                 $qb->expr()->eq('hash', '?'),
                 $qb->expr()->gt('expires', '?')
            ))
            ->setParameter(0, $hash)
            ->setParameter(1, time())
            ->execute()
            ->fetch();
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
        if ($expires < 60 * 60 or $expires > 60 * 60 * 24 * 30) {
            $expires = 60 * 60;
        }
        $expires = time() + $expires;

        $hash = substr(md5(uniqid() . $userName), 0, 10);

        $qb = $this->db->createQueryBuilder();
        $qb->insert($this->table)
            ->values(array(
                'hash' => '?',
                'userName' => '?',
                'password' => '?',
                'comment' => '?',
                'expires' => '?',
            ))
            ->setParameter(0, $hash)
            ->setParameter(1, $userName)
            ->setParameter(2, $password)
            ->setParameter(3, $comment)
            ->setParameter(4, $expires)
            ->execute();

        return $hash;
    }

    /**
     * Delete credential
     *
     * @param string $hash Identifier for credentials to delete
     * @return void
     */
    public function delete($hash)
    {
        $qb = $this->db->createQueryBuilder();
        $qb->delete($this->table)
            ->where(
                $qb->expr()->eq('hash', '?')
            )
            ->setParameter(0, $hash)
            ->execute();

        return $hash;
    }

    /**
     * Clean expired credentials from the db
     *
     * @return void
     */
    public function clean()
    {
        $qb = $this->db->createQueryBuilder();
        $qb->delete($this->table)
            ->where($qb->expr()->lt('expires', ':expires'))
            ->setParameter('expires', time())
            ->execute();
    }
}
