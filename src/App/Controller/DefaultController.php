<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Model\CredentialService;
use Twig_Environment;

class DefaultController
{
    protected $app;
    protected $twig;
    protected $credentialService;
    protected $request;

    /**
     * Constructor
     *
     * @param mixed $app
     * @param \Twig_Environment $twig
     * @param DoctrineConnection $db
     * @param Request $request
     * @return void
     */
    public function __construct($app, Twig_Environment $twig, CredentialService $credentialService, Request $request)
    {
        $this->app = $app;
        $this->twig = $twig;
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
            $comment = $this->request->get('comment');

            // Exit if we got no password
            if (empty($password)) {
                return $this->twig->render('index.twig');
            }

            $period = $this->request->get('period', 60 * 60);
            $hash = $this->credentialService->save($userName, $password, $comment, $period);

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
            $credentials = $this->credentialService->delete($hash);
        }

        return $this->app->redirect('/');
    }
}
