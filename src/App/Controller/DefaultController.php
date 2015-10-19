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

    /**
     * Constructor
     *
     * @param mixed $app
     * @param Twig_Environment $twig
     * @param CredentialService $credentialService
     */
    public function __construct($app, Twig_Environment $twig, CredentialService $credentialService)
    {
        $this->app = $app;
        $this->twig = $twig;
        $this->credentialService = $credentialService;

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
        $hash = $this->credentialService->save($userName, $password, $comment, $period);

        return $this->app->redirect($this->app['baseUrl'] . '/link/' . $hash);
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
     * @param Request $request
     * @return mixed Rendered Twig template
     */
    public function deleteAction(Request $request)
    {
        $hash = $request->get('hash');

        if ($hash) {
            $this->credentialService->delete($hash);
        }

        return $this->app->redirect($this->app['baseUrl'] . '/');
    }
}
