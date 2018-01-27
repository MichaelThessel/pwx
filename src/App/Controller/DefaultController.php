<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use App\Service\CredentialsService;

class DefaultController
{

    private $transport;
    private $message;

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
        $password = $request->get('password');
        $sendByEmail = $request->get('sendByEmail');
        $userEmail = $request->get('userEmail');

        // Exit if we got no password
        if (empty($password))
            // fix, redirect to base url not to root.
            return $this->app->redirect($this->app['baseUrl']);
        // Exit if we got no email adress
        if($sendByEmail == true && empty($userEmail))
            return $this->app->redirect($this->app['baseUrl']);

        $credentials = $this->saveCredentials($request);

        $sendByEmail = $request->get('sendByEmail');

        // check if this records needs to be send out and is allowed
        if($sendByEmail == true && $this->app['email_active'] == true) {
            $sendMethod = $this->app['email']['method'];

            switch ($sendMethod) {
            case "local":
                $this->transport = \Swift_MailTransport::newInstance();
                break;
            case "smtp":
                $this->transport = new \Swift_SmtpTransport($this->app['email']['server'], $this->app['email']['port'], 'ssl');
                $this->transport->setUsername($this->app['email']['username']);
                $this->transport->setPassword($this->app['email']['password']);
                break;
            case "sendmail":
                $this->transport = new \Swift_SendmailTransport($this->app['email']['sendmail_path']);
                break;
            }

            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($this->transport);

            $this->message = new \Swift_Message($this->app['email']['subject']);
            $this->message->setFrom($this->app['email']['from_address']);
            $this->message->setTo(array($request->get('userEmail')));

            $body = $this->twig->render('email.twig', array(
                'hash' => $credentials->getHash(),
            ));

            $this->message->setBody($body, 'text/html');
            $mailer->send($this->message, $failedRecipients);

        }

        return $this->app->redirect($this->app['baseUrl'] . '/link/' . $credentials->getHash());
    }

    /**
     * Api call to submit new data
     *
     * @param Request $request
     * @return string JSON Response
     */
    public function apiPostAction(Request $request)
    {
        // Exit if we got no password
        $password = $request->get('password');
        if (empty($password)) {
            $error = array('message' => 'The password cannot be empty');
            return $this->app->json($error, Response::HTTP_BAD_REQUEST);
        }

        $credentials = $this->saveCredentials($request);

        $link = array('hash' => $credentials->getHash());
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
        $credentials = $this->getCredentials($hash);

        return $this->twig->render('view_password.twig', array(
            'credentials' => $credentials,
        ));
    }

    /**
     * Api call to view an entry
     *
     * @param mixed $hash Hash that identifies the entry
     * @return string JSON Response
     */
    public function apiViewAction($hash)
    {
        $credentials = $this->getCredentials($hash);

        if (!$credentials) {
            return $this->app->json(
                array('message' => 'This recored has expired'),
                Response::HTTP_GONE
            );
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
        $this->deleteCredentials($request->get('hash'));

        return $this->app->redirect($this->app['baseUrl'] . '/');
    }

    /**
     * Api call to delete an entry
     *
     * @param $hash
     * @return string JSON Response
     */
    public function apiDeleteAction($hash)
    {
        $this->deleteCredentials($hash);

        return $this->app->json('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Extract credentialParameter from Post-Request and save it
     *
     * @param Request $request
     * @return \App\Entity\Credentials
     */
    protected function saveCredentials(Request $request)
    {
        return $this->credentialsService->save(array(
            'userName' => $request->get('userName'),
            'password' => $request->get('password'),
            'comment' => $request->get('comment'),
            'expires' => $request->get('expires', 60 * 60),
            'oneTimeView' => (int) $request->get('oneTimeView') == 1,
        ));
    }

    /**
     * Get credentials by hash from the credentialService
     *
     * @param $hash
     * @return \App\Entity\Credentials
     */
    protected function getCredentials($hash)
    {
        $credentials = $this->credentialsService->find($hash);

        if ($credentials && $credentials->getOneTimeView()) {
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
