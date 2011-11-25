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

        // Check I'm on one page checkout
        $location = self::$browser->getLocation();
        $this->assertRegexp('#/checkout/onepage/$#', $location);

        self::$browser
            // Checkout as guest
            ->click(Selenium\Locator::xpath('//label[@for="login:guest"]'))
            ->click(Selenium\Locator::id('onepage-guest-register-button'))

            // Billing
            ->waitForCondition('selenium.browserbot.getCurrentWindow().document.getElementById("opc-billing").getAttribute("class").indexOf("active") !== false', self::TIMEOUT)
            ->type(Selenium\Locator::id('billing:firstname'),    'Firstname')
            ->type(Selenium\Locator::id('billing:lastname'),     'Lastname')
            ->type(Selenium\Locator::id('billing:company'),      'Company')
            ->type(Selenium\Locator::id('billing:email'),        'email@example.org')
            ->type(Selenium\Locator::id('billing:street1'),      '1 rue de la facturation')
            ->type(Selenium\Locator::id('billing:city'),         'Lille')
            ->type(Selenium\Locator::id('billing:postcode'),     '59000')
            ->type(Selenium\Locator::id('billing:telephone'),    '0671736698')
            ->select(Selenium\Locator::id('billing:country_id'), 'France')
            ->select(Selenium\Locator::id('billing:region_id'),  'Nord')
            ->click(Selenium\Locator::css('#billing-buttons-container button'))

            // Shipping method
            ->waitForCondition('selenium.browserbot.getCurrentWindow().document.getElementById("opc-shipping_method").getAttribute("class").indexOf("active") !== false', self::TIMEOUT)

        // @todo: quick-fix
        ;sleep(2);self::$browser

            ->click(Selenium\Locator::css('#shipping-method-buttons-container button'))

            // Payment
            ->waitForCondition('selenium.browserbot.getCurrentWindow().document.getElementById("opc-payment").getAttribute("class").indexOf("active") !== false', self::TIMEOUT)

        // @todo: quick-fix
        ;sleep(2);self::$browser

            ->click(Selenium\Locator::xpath('//label[@for="p_method_checkmo"]'))
            ->click(Selenium\Locator::css('#payment-buttons-container button'))

            // Review
            ->waitForCondition('selenium.browserbot.getCurrentWindow().document.getElementById("opc-review").getAttribute("class").indexOf("active") !== false', self::TIMEOUT)
        ;

        // @todo: quick-fix
        sleep(2);

        // Checks
        $expected = '4 005,00 €';
        $actual = self::$browser->getText(Selenium\Locator::css("#checkout-review-table tr.last td.last span.price"));
        $this->assertEquals($expected, $actual);

        // Place order
        self::$browser
            ->click(Selenium\Locator::css('#review-buttons-container button'))
            ->waitForPageToLoad(self::TIMEOUT)
        ;

        // Check confirmation
        $expected = 'Thank you for your purchase!';
        $actual = self::$browser->getText(Selenium\Locator::css('div.col-main h2.sub-title'));
        $this->assertEquals($expected, $actual);
    }
}
