<?php

namespace App\Controller;

use App\Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use App\Model\CredentialService;
use App\Model\ForceSSLService;
use Twig_Environment;

class DefaultController
{
    protected $app;
    protected $twig;
    protected $credentialService;
    protected $forceSSLService;
    protected $request;

    /**
     * Constructor
     *
     * @param mixed $app
     * @param Twig_Environment $twig
     * @param CredentialService $credentialService
     * @param ForceSSLService $forceSSLService
     * @param Request $request
     */
    public function __construct(Application $app, Twig_Environment $twig, CredentialService $credentialService, ForceSSLService $forceSSLService, Request $request)
    {
        $this->app = $app;
        $this->twig = $twig;
        $this->credentialService = $credentialService;
        $this->forceSSLService = $forceSSLService;
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
        $this->forceSSLService->forceSSLIfSet();

        if ($this->request->getMethod() == 'POST') {
            $userName = $this->request->get('userName');
            $password = $this->request->get('password');
            $comment = $this->request->get('comment');

            // Exit if we got no password
            if (empty($password)) {
                return $this->twig->render('index.twig');
            }

            $period = $this->request->get('period', 60 * 60);
            $hash = $this->credentialService->save($userName, $password, $comment, $period);

            return $this->app->redirect('/link/' . $hash);
        }

        return $this->twig->render('index.twig');
    }

    /**
     * View the share link
     *
     * @param mixed $hash Hash that identifies the entry
     * @return mixed Rendered Twig template
     */
    public function viewLinkAction($hash)
    {
        $this->forceSSLService->forceSSLIfSet();

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
        $this->forceSSLService->forceSSLIfSet();

        $credentials = $this->credentialService->get($hash);

        return $this->twig->render('view_password.twig', array(
            'userName' => $credentials['userName'],
            'password' => $credentials['password'],
            'comment' => $credentials['comment'],
            'expires' => $credentials['expires'] * 1000,
            'hash' => $hash,
        ));
    }

    /**
     * Delete an entry
     *
     * @return mixed Rendered Twig template
     */
    public function deleteAction()
    {
        $hash = $this->request->get('hash');

        if ($hash) {
            $this->credentialService->delete($hash);
        }

        return $this->app->redirect('/');
    }
}
