<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Service\CredentialsService;
use Symfony\Component\HttpFoundation\Response;

class DefaultApiController
{
    /** @var \Pimple */
    protected $app;

    /** @var CredentialsService $credentialsService **/
    protected $credentialsService;

    /**
     * Constructor
     *
     * @param mixed $app
     * @param CredentialsService $credentialsService
     */
    public function __construct(
        $app,
        CredentialsService $credentialsService
    )
    {
        $this->app = $app;
        $this->credentialsService = $credentialsService;

        // Clean the credentials table on every request (workaround to not have to add cronjobs)
        $this->credentialsService->deleteExpired();
    }

    /**
     * Submit new data via api-call
     *
     * @param Request $request
     * @return mixed Rendered Twig template
     */
    public function apiPostAction(Request $request)
    {
        $userName = $request->get('userName');
        $password = $request->get('password');
        $comment = $request->get('comment');
        $expires = $request->get('expires', 60 * 60);

        // Exit if we got no password
        if (empty($password)) {
            return json_encode(array('Please submit password'));
        }

        /** @var \App\Entity\Credentials $credentials */
        $credentials = $this->credentialsService->save(array(
            'userName' => $userName,
            'password' => $password,
            'comment' => $comment,
            'expires' => $expires,
        ));

        return json_encode(array(
            'link' => $this->app['baseUrl'] . '/api/' . $credentials->getHash()
        ));
    }

    /**
     * View an entry
     *
     * @param mixed $hash Hash that identifies the entry
     * @return string
     */
    public function apiGetAction($hash)
    {
        $credentials = $this->credentialsService->find($hash);

        if (!$credentials)
        {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        return json_encode($credentials);
    }

    /**
     * Delete an entry
     *
     * @param $hash
     * @return mixed Rendered Twig template
     */
    public function apiDeleteAction($hash)
    {
        $this->credentialsService->delete($hash);
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
