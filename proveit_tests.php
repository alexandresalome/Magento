<?php

require_once __DIR__.'/vendor/php-selenium/autoload.php';

class myQuickTest extends PHPUnit_Framework_TestCase
{
    const TIMEOUT = 60000;

    /**
     * @var Selenium\Browser
     */
    protected static $browser;

    public static function setUpBeforeClass()
    {
        $client = new Selenium\Client();
        self::$browser = $client->getBrowser('http://magento.local');
        self::$browser->start();
    }

    public function testHomepage()
    {
        self::$browser
            ->open('/')
            ->waitForPageToLoad(self::TIMEOUT)
        ;

        // Check we have the logo
        $this->assertEquals('Magento Commerce', self::$browser->getText(Selenium\Locator::css('h1.logo strong')));

        // Check I have "Voitures" in the menu
        $this->assertEquals(1, self::$browser->getXpathCount('//ul[@id="nav"]//a//span[contains(., "Voitures")]'));

    }

    public function testCatalog()
    {
        // Click "Voitures"
        self::$browser
            ->open('/')
            ->waitForPageToLoad(self::TIMEOUT)
            ->click(Selenium\Locator::linkContaining(Selenium\Pattern::glob('Voitures')))
            ->waitForPageToLoad(self::TIMEOUT)
        ;

        // Check URL
        $url = self::$browser->getLocation();
        $this->assertRegExp('/voitures\.html$/', $url);

        // Check amount of items
        $amount = self::$browser->getText(Selenium\Locator::css('p.amount'));
        $this->assertEquals('1 Item(s)', $amount);
    }

    public function testProduct()
    {
        self::$browser
            ->open('/voitures.html')
            ->waitForPageToLoad(self::TIMEOUT)
            ->click(Selenium\Locator::linkContaining('Voiture de course'))
            ->waitForPageToLoad(self::TIMEOUT)
        ;

        $location = self::$browser->getLocation();
        $this->assertRegexp('#/voitures/voiture-de-course\.html$#', $location);
    }

    public static function tearDownAfterClass()
    {
        self::$browser->stop();
    }
}
