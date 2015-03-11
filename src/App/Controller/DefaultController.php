<?php

namespace App\Controller;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use App\Model\CredentialService;

class DefaultController
{
    protected $app;
    protected $twig;
    protected $logger;
    protected $credentialService;
    protected $request;

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
    public function __construct($app, \Twig_Environment $twig, Logger $logger, CredentialService $credentialService, Request $request)
    {
        $this->app = $app;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->credentialService = $credentialService;
        $this->request = $request;

        // Clean the db on every request (workaround to not have to add cronjobs)
        $this->credentialService->clean();
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

            $period = $this->request->get('period', 60 * 60);
            $hash = $this->credentialService->save($userName, $password, $period);

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
        $credentials = $this->credentialService->get($hash);

        return $this->twig->render('view.twig', array(
            'userName' => $credentials['userName'],
            'password' => $credentials['password'],
            'expires' => $credentials['expires'] * 1000,
            'hash' => $hash,
        ));
    }
}
