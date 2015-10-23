<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;
use App\Service\CredentialsService;

class DefaultController
{
    protected $app;
    protected $twig;
    protected $credentialsService;

    /**
     * Constructor
     *
     * @param mixed $app
     * @param Twig_Environment $twig
     * @param CredentialsService $credentialsService
     */
    public function __construct(
        $app,
        Twig_Environment $twig,
        CredentialsService $credentialsService
    )
    {
        $this->app = $app;
        $this->twig = $twig;
        $this->credentialsService = $credentialsService;

        // Clean the credentials table on every request (workaround to not have to add cronjobs)
        $this->credentialsService->deleteExpired();
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

        $expires = $request->get('expires', 60 * 60);

        $credentials = $this->credentialsService->save(array(
            'userName' => $userName,
            'password' => $password,
            'comment' => $comment,
            'expires' => $expires,
        ));

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
        $credentials = $this->credentialsService->find($hash);

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

        $this->credentialsService->delete($hash);

        return $this->app->redirect($this->app['baseUrl'] . '/');
    }
}
