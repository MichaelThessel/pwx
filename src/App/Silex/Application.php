<?php
namespace App\Silex;

use Aptoma\Silex\Application as BaseApplication;
use Symfony\Component\Debug\Debug;

class Application extends BaseApplication
{
    public function __construct(array $values = array())
    {
        if ($values['debug']) {
            Debug::enable();
        }

        parent::__construct($values);

        $this->registerTwig($this);
        $this->registerLogger($this);
    }
}
