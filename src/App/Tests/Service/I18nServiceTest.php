<?php

namespace AppBundle\Tests\Service;

use PHPUnit_Framework_TestCase;

class I18nServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var \App\Service\I18nService */
    protected $i18nService;

    /** @var  \Pimple */
    protected $app;

    public function setUp()
    {
        $this->app = require __DIR__.'/../../../../app/app.php';
        $this->i18nService = $this->app['i18n_service'];
    }

    /**
     * Test retrieval of valid locales
     *
     * @return void
     */
    public function testGetValidLocales()
    {
        $validLocales = $this->i18nService->getValidLocales();

        $this->assertTrue(is_array($validLocales));
        $this->assertNotEmpty($validLocales);
    }

    /**
     * Test setting the locale by cookie
     *
     * @return void
     */
    public function testSetLocaleByCookie()
    {
        $_COOKIE['locale'] = $this->getRandomLocale();

        $locale = $this->i18nService->getLocale();

        $this->assertSame($_COOKIE['locale'], $locale);

        unset($_COOKIE['locale']);
    }

    /**
     * Test setting the locale by get param
     *
     * @return void
     */
    public function testSetLocaleByGet()
    {

        $_GET['locale'] = $this->getRandomLocale();

        $locale = $this->i18nService->getLocale();

        $this->assertSame($_GET['locale'], $locale);

        unset($_GET['locale']);
    }

    /**
     * Test that GET takes precedence before COOKIE
     *
     * @return void
     */
    public function testGetCookiePrecedence()
    {

        $locales = $this->i18nService->getValidLocales();

        // Make sure we got enough locales to test with
        if (count($locales) < 2) return;

        $_GET['locale'] = $locales[0];
        $_COOKIE['locale'] = $locales[1];

        $locale = $this->i18nService->getLocale();

        $this->assertSame($_GET['locale'], $locale);

        unset($_GET['locale']);
        unset($_COOKIE['locale']);
    }

    /**
     * Tests if an invalid config locale will throw an exception
     *
     * @expectedException \Exception
     *
     * @return void
     */
    public function testInvalidConfigLocale()
    {
        $this->i18nService->getLocale('invalidlocale');
    }

    /**
     * Returns a random valid locale
     */
    protected function getRandomLocale()
    {
        $validLocales = $this->i18nService->getValidLocales();
        $locale = rand(0, count($validLocales) - 1);
        return $validLocales[$locale];
    }
}
