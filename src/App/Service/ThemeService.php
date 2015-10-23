<?php

namespace App\Service;

/**
 * Theme service
 *
 * Handles theme selection
 */
class ThemeService {

    protected $validThemes = array(
        'cerulean',
        'cosmo',
        'cyborg',
        'darkly',
        'flatly',
        'journal',
        'lumen',
        'paper',
        'readable',
        'sandstone',
        'simplex',
        'slate',
        'spacelab',
        'superhero',
        'united',
        'yeti',
    );

    /**
     * Constructor
     *
     * @param mixed $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get list of valid themes
     *
     * @return array Valid themes
     */
    public function getValidThemes()
    {
        return $this->validThemes;
    }

    /**
     * Load theme from cookie if cookie is set and valid
     *
     * @return void
     */
    public function setUserTheme()
    {
        if (isset($_COOKIE['theme']) && in_array($_COOKIE['theme'], $this->getValidThemes())) {
            $this->app['theme'] = $_COOKIE['theme'];
        }
    }

}
