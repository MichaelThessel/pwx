<?php

namespace App\Controller;

use App\Entity\Credentials;
use App\Model\CredentialsFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class DefaultController
{
    /** @var EntityManager */
    protected $em;

    /** @var \Pimple */
    protected $app;

    /** @var Twig_Environment  */
    protected $twig;

    /**
     * Constructor
     *
     * @param mixed $app
     * @param Twig_Environment $twig
     */
    public function __construct($app, Twig_Environment $twig)
    {
        $this->app = $app;
        $this->twig = $twig;
        $this->em = $app['orm.em'];

        // Clean the db on every request (workaround to not have to add cronjobs)
        $this->em->getRepository('App\Entity\Credentials')
            ->clean();
    }

    /**
     * Create a new entry
     *
     * @return mixed Rendered Twig template
     */
    public function indexAction()
    {
        return $this->twig->render('index.twig');
    }

    /**
     * Submit new data
     *
     * @param Request $request
     * @return mixed Rendered Twig template
     */
    public function indexPostAction(Request $request)
    {
        $userName = $request->get('userName');
        $password = $request->get('password');
        $comment = $request->get('comment');

        // Exit if we got no password
        if (empty($password)) {
            return $this->twig->render('index.twig');
        }

        $period = $request->get('period', 60 * 60);

        // Generate a new CredentialsObject
        $credentials = CredentialsFactory::createCredentials($userName, $password, $comment, $period);

        /** @var EntityManager $em */
        $this->em->persist($credentials);
        $this->em->flush();

        return $this->app->redirect($this->app['baseUrl'] . '/link/' . $credentials->getHash());
    }

    /**
     * View the share link
     *
     * @param mixed $hash Hash that identifies the entry
     * @return mixed Rendered Twig template
     */
    public function viewLinkAction($hash)
    {
        return $this->twig->render('view_link.twig', array(
            'hash' => $hash,
        ));
    }

    /**
     * View an entry
     *
     * @param mixed $hash Hash that identifies the entry
     * @return mixed Rendered Twig template
     */
    public function viewPasswordAction($hash)
    {
        /** @var Credentials $credentials $credentials */
        $credentials = $this->em->getRepository('App\Entity\Credentials')
            ->findNotExpiredByHash($hash);

        if (!$credentials)
        {
            /**
             * ToDo
             * Throw a resourceNotFoundException
             * Or render a resourceNotFound-Template
             * passing a password=null keeps the behavior and let pass the functional tests
             */
            return $this->twig->render('view_password.twig', array(
                'password' => null,
                'hash' => $hash,
            ));
        }

        return $this->twig->render('view_password.twig', array(
            'userName' => $credentials->getUsername(),
            'password' => $credentials->getPassword(),
            'comment' => $credentials->getComment(),
            'expires' => $credentials->getExpires() * 1000,
            'hash' => $hash,
        ));
    }

    /**
     * Delete an entry
     *
     * @param Request $request
     * @return mixed Rendered Twig template
     */
    public function deleteAction(Request $request)
    {
        $hash = $request->get('hash');

        if ($hash) {
            $credentials = $this->em->getRepository('App\Entity\Credentials')
                ->findOneBy(array(
                    'hash' => $hash
                ));
            if ($credentials)
            {
                $this->em->remove($credentials);
                $this->em->flush();
            }
        }

        return $this->app->redirect($this->app['baseUrl'] . '/');
    }
}
