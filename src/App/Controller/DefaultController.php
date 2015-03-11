<?php

namespace App\Controller;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection as DoctrineConnection;

class DefaultController
{
    protected $twig;

    /**
     * Constructor
     *
     * @param mixed $app
     * @param \Twig_Environment $twig
     * @param Logger $logger
     * @param DoctrineConnection $db
     * @param Request $request
     * @return void
     */
    public function __construct($app, \Twig_Environment $twig, Logger $logger, DoctrineConnection $db, Request $request)
    {
        $this->app = $app;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->db = $db;
        $this->request = $request;

        // Clean the db on every request (workaround to not have to add cronjobs)
        $this->cleanDB();
    }

    /**
     * Create a new entry
     *
     * @return mixed Rendered Twig template
     */
    public function indexAction()
    {
        if ($this->request->getMethod() == 'POST') {
            $userName = $this->request->get('userName');
            $password = $this->request->get('password');

            // Exit if we got no username or password
            if (empty($userName) or empty($password)) {
                return $this->twig->render('index.twig');
            }

            $expires = $this->request->get('period', 60 * 60);
            if ($expires < 60 * 60 or $expires > 60 * 60 * 24 * 30) {
                $expires = 60 * 60;
            }
            $expires = time() + $expires;

            $hash = substr(md5(uniqid() . $userName), 0, 10);

            $qb = $this->db->createQueryBuilder();
            $query = $qb->insert('credentials')
                ->values(array(
                    'hash' => '?',
                    'userName' => '?',
                    'password' => '?',
                    'expires' => '?',
                ))
                ->setParameter(0, $hash)
                ->setParameter(1, $userName)
                ->setParameter(2, $password)
                ->setParameter(3, $expires)
                ->execute();

            return $this->app->redirect('/pw/' . $hash);
        }

        return $this->twig->render('index.twig');
    }

    /**
     * View an entry
     *
     * @param mixed $hash Hash that identifies the entry
     * @return mixed Rendered Twig template
     */
    public function viewAction($hash)
    {
        $qb = $this->db->createQueryBuilder();
        $query = $qb->select('userName', 'password', 'expires')
            ->from('credentials')
           ->where('hash = ? AND expires >= ?')
            ->setParameter(0, $hash)
            ->setParameter(1, time());

        $credentials = $query->execute()->fetch();

        return $this->twig->render('view.twig', array(
            'userName' => $credentials['userName'],
            'password' => $credentials['password'],
            'expires' => $credentials['expires'] * 1000,
        ));
    }

    /*
     * Deletes expired entires
     */
    protected function cleanDB() {
        $qb = $this->db->createQueryBuilder('credentials');
        $qb->delete('credentials')
            ->where($qb->expr()->lt('expires', ':expires'))
            ->setParameter('expires', time())
            ->execute();
    }
}
