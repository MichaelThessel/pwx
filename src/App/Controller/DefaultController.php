<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        // Exit if we got no password
        if (empty($request->get('password'))) {
            return $this->app->redirect('/');
        }

        $credentials = $this->saveCredentials($request);

        return $this->app->redirect($this->app['baseUrl'] . '/link/' . $credentials->getHash());
    }

    /**
     * Api call to submit new data
     *
     * @param Request $request
     * @return mixed Rendered Twig template
     */
    public function apiPostAction(Request $request)
    {
        // Exit if we got no password
        if (empty($request->get('password'))) {
            $error = array('message' => 'The password can not be empty.');
            return $this->app->json($error, Response::HTTP_BAD_REQUEST);
        }

        $credentials = $this->saveCredentials($request);

        $link = array('link' => $this->app['baseUrl'] . '/api/' . $credentials->getHash());
        return $this->app->json($link);
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
        $credentials = $this->retrieveCredentials($hash);

        return $this->twig->render('view_password.twig', array(
            'credentials' => $credentials,
        ));
    }

    /**
     * Api call to view an entry
     *
     * @param mixed $hash Hash that identifies the entry
     * @return mixed Rendered Twig template
     */
    public function apiViewPasswordAction($hash)
    {
        $credentials = $this->retrieveCredentials($hash);

        if (!$credentials)
        {
            return $this->app->json('', Response::HTTP_NOT_FOUND);
        }

        return $this->app->json($credentials);
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
        $this->deleteCredentials($hash);

        return $this->app->redirect($this->app['baseUrl'] . '/');
    }

    /**
     * Api call to delete an entry
     *
     * @param $hash
     * @return mixed Rendered Twig template
     */
    public function apiDeleteAction($hash)
    {
        $this->deleteCredentials($hash);
        return $this->app->json('',Response::HTTP_NO_CONTENT);
    }

    /**
     * Extract credentialParameter from Post-Request and save it
     *
     * @param Request $request
     * @return \App\Entity\Credentials
     */
    protected function saveCredentials(Request $request)
    {
        $userName = $request->get('userName');
        $password = $request->get('password');
        $comment = $request->get('comment');
        $oneTimeView = (int) $request->get('oneTimeView') == 1;

        $expires = $request->get('expires', 60 * 60);

        /** @var \App\Entity\Credentials $credentials */
        $credentials = $this->credentialsService->save(array(
            'userName' => $userName,
            'password' => $password,
            'comment' => $comment,
            'expires' => $expires,
            'oneTimeView' => $oneTimeView
        ));

        return $credentials;
    }

    /**
     * Retrieve credentials by hash from the credentialService
     *
     * @param $hash
     * @return \App\Entity\Credentials
     */
    protected function retrieveCredentials($hash)
    {
        $credentials = $this->credentialsService->find($hash);

        if ($credentials && $credentials->getOneTimeView())
        {
            $this->credentialsService->delete($credentials->getHash());
        }

        return $credentials;
    }

    /**
     * Delete credentials by hash in the credentialService
     * 
     * @param $hash
     */
    protected function deleteCredentials($hash)
    {
        $this->credentialsService->delete($hash);
    }

}
