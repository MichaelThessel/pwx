<?php

namespace AppBundle\Tests\Service;

use PHPUnit_Framework_TestCase;
use App\Service\ThemeService;

class ThemeServiceTest extends PHPUnit_Framework_TestCase
{
    protected $themeService;
    protected $app;

    public function setUp()
    {
        $this->app = require __DIR__.'/../../../../app/app.php';
        $this->themeService = $this->app['theme_service'];
    }

    /**
     * Test retrieval of valid themes
     *
     * @return void
     */
    public function testGetValidThemes()
    {
        $validThemes = $this->themeService->getValidThemes();

        $this->assertTrue(is_array($validThemes));
        $this->assertNotEmpty($validThemes);
    }

    /**
     * Test setting the theme by cookie
     *
     * @return void
     */
    public function testSetUserTheme()
    {
        $validThemes = $this->themeService->getValidThemes();

        // Select a theme from the middle
        $theme = (int ) round(count($validThemes) / 2);

        $_COOKIE['theme'] = $validThemes[$theme];

        $this->themeService->setUserTheme();

        $this->assertSame($_COOKIE['theme'], $this->app['theme']);
    }
}
