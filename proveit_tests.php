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

        // Check URL
        $location = self::$browser->getLocation();
        $this->assertRegexp('#/voitures/voiture-de-course\.html$#', $location);

        // Check title
        $xpath = '//div[@class="product-name"]//h1';
        $this->assertEquals(1, self::$browser->getXpathCount($xpath));
        $this->assertEquals("Voiture de course", self::$browser->getText(Selenium\Locator::xpath($xpath)));
    }

    public function testCartToCheckout()
    {
        self::$browser
            ->open('/voitures/voiture-de-course.html')
            ->waitForPageToLoad(self::TIMEOUT)
        ;

        self::$browser
            ->click(Selenium\Locator::css('div.add-to-cart button'))
            ->waitForPageToLoad(self::TIMEOUT)
        ;

        $expected = 'Voiture de course was added to your shopping cart.';
        $actual   = self::$browser->getText(Selenium\Locator::css('ul.messages li.success-msg span'));

        $this->assertEquals($expected, $actual);

        self::$browser
            ->click(Selenium\Locator::css('ul.checkout-types button.btn-checkout'))
            ->waitForPageToLoad(self::TIMEOUT)
        ;
    }

    public static function tearDownAfterClass()
    {
        self::$browser->stop();
    }
}
