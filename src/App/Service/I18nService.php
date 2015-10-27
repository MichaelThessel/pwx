<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * I18n service
 *
 * Handles language selection
 */
class I18nService {

    protected $validLocales = array(
        'en',
        'de',
        'eo',
        'es',
    );

    /**
     * Get list of valid locales
     *
     * @return array Valid locales
     */
    public function getValidLocales()
    {
        return $this->validLocales;
    }

    /**
     * Get the current locale from request, cookie or config
     *
     * @param string $configLocale Configuration locale setting
     * @throws \Exception
     * @return string Locale
     */
    public function getLocale($configLocale = 'en')
    {
        // Using superglobals here as the Request object is not available at this point
        if (isset($_GET['locale']) && $this->isValidLocale($_GET['locale'])) {
            return $_GET['locale'];
        } elseif (isset($_COOKIE['locale']) && $this->isValidLocale($_COOKIE['locale'])) {
            return $_COOKIE['locale'];
        } elseif ($this->isValidLocale($configLocale)) {
            return $configLocale;
        }

        throw new \Exception('Invalid locale set in config');
    }

    /**
     * Save the locale in a cookie if the request contains a locale query string
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function setLocaleCookie(Request $request, Response $response)
    {
        $locale = $request->get('locale');
        if ($this->isValidLocale($locale)) {
            $response->headers->setCookie(new Cookie('locale', $locale, time() + 365 * 86400, '/', null, false, false));
        }
    }

    /**
     * Test whether or not a locale string is valid
     *
     * @param mixed $locale Locale to test
     * @return bool Whether or not the locale is valid
     */
    protected function isValidLocale($locale)
    {
        return in_array($locale, $this->validLocales);
    }
}
