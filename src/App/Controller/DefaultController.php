<?php

namespace App\Controller;

use App\Entity\Credentials;
use App\Entity\CredentialsRepository;
use App\Model\CredentialsFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class DefaultController
{
    /** @var \Pimple */
    protected $app;

    /** @var Twig_Environment */
    protected $twig;

    /** @var EntityManager */
    protected $em;

    /** @var CredentialsFactory */
    protected $credentialsFactory;

    /** @var CredentialsRepository */
    protected $credentialsRepository;

    /**
     * Constructor
     *
     * @param mixed $app
     * @param Twig_Environment $twig
     * @param EntityManager $em
     * @param CredentialsFactory $credentialsFactory
     * @param CredentialsRepository $credentialsRepository
     */
    public function __construct(
        $app,
        Twig_Environment $twig,
        EntityManager $em,
        CredentialsFactory $credentialsFactory,
        CredentialsRepository $credentialsRepository
    )
    {
        $this->app = $app;
        $this->twig = $twig;
        $this->em = $em;
        $this->credentialsFactory = $credentialsFactory;
        $this->credentialsRepository = $credentialsRepository;

        // Clean the db on every request (workaround to not have to add cronjobs)
        $this->credentialsRepository->clean();
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
        $credentials = $this->credentialsFactory->getInstance();
        $credentials->setUsername($userName);
        $credentials->setPassword($password);
        $credentials->setComment($comment);
        $credentials->setExpires($period);

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
        $credentials = $this->credentialsRepository->find($hash);

        return $this->twig->render('view_password.twig', array(
            'credentials' => $credentials,
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
            $credentials = $this->credentialsRepository->findOneBy(array('hash' => $hash));
            if ($credentials) {
                $this->em->remove($credentials);
                $this->em->flush();
            }
        }

        return $this->app->redirect($this->app['baseUrl'] . '/');
    }
}
