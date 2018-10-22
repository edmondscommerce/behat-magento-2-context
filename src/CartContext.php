<?php namespace EdmondsCommerce\BehatMagentoTwoContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Exception\ExpectationException;

class CartContext extends AbstractMagentoContext implements Context, SnippetAcceptingContext
{
    const TESTPRODUCTURI = 'testProductUri';

    private $productQty;
    private $testProductName;

    public function __construct()
    {
        if (isset(self::$_magentoSetting[self::TESTPRODUCTURI])){
            $this->testProductName = str_replace('.html','', (self::$_magentoSetting[self::TESTPRODUCTURI]));
        }
    }


    /**
     * @Given I have my minicart open
     * @Then I open the minicart
     * @throws \Exception
     */
    public function iOpenTheMiniCart()
    {
        $this->_html->iClickOnTheElement('a.action.showcart');
    }

    /**
     * @Given /^I go to the test product page$/
     */
    public function iGoToTheTestProductPage()
    {
        $path = AbstractMagentoContext::$_magentoSetting['testProductUri'];
        $this->visitPath("/$path");
    }

    /**
     * @When /^I click on the add to cart button$/
     */
    public function iClickOnTheAddToCartButton()
    {

    }

    /**
     * @Given /^I click on the Remove Product Item link$/
     */
    public function iClickOnTheRemoveProductLink()
    {
        $el = $this->getSession()->getPage()->find('css', 'a.action.action-delete');

        $el->click();
    }

    /**
     * @When /^I click on the clear shopping cart button$/
     * @throws ExpectationException
     */
    public function clickOnClearShoppingCartButton()
    {
        $clearBasket = $this->getSession()->getPage()->find('css', '#empty_cart_button');
        if ($clearBasket) {
            $clearBasket->click();
        }
    }

    /**
     * @Given /^I have an empty cart$/
     * @throws \Exception
     */
    public function haveAnEmptyCart()
    {
        $this->visitPath('/checkout/cart');
        // find clear shopping cart button
        if ($this->getSession()->getPage()->find('xpath', '//button[@id=\'empty_cart_button\']')) {
            $this->clickOnClearShoppingCartButton();
        }
    }

    /**
     * @Given /^I am on the cart page$/
     * @throws \Exception
     */
    public function iGoToTheCartPage()
    {
        $this->visitPath('/checkout/cart');
    }

    /**
     * @Then /^I should see item has been added to the cart$/
     */
    public function iShouldSeeItemHasBeenAddedToTheCart()
    {
        $this->_html->waitformilliseconds(500);
        $this->getSession()->getPage()->find('css', 'div.message-success');
    }


    /**
     * @Given I add a different product
     */
    public function iAddADifferentProduct()
    {
        $altProductUri = AbstractMagentoContext::$_magentoSetting['altProduct'];
        $this->iGoToTheProductPage($altProductUri);

    }

    /**
     * @Given /^I go to my wishlist$/
     */
    public function iGoToMyWishlist()
    {
        $this->_mink->visitPath('/wishlist/index');
        $this->getSession()->getPage()->find('xpath', '//h1[@class=\'page-title\']//span[contains(text(), \'My Wish List\')]');
    }

    /**
     * @Given /^I should have an empty wishlist$/
     */
    public function iShouldHaveAnEmptyWishlist()
    {
        $this->iGoToMyWishlist();
        $this->getSession()->getPage()->hasContent('You have no items in your wish list.');
    }

    /**
     * @When /^I press the add to wishlist button$/
     */
    public function iPressTheAddToWishlistButton()
    {
        $this->getSession()->getPage()->find('xpath', '//span[contains(text(),\'Add to Wish List\')]')->click();
    }

    /**
     * @Given /^I add a random product to my wish list$/
     * @throws \Exception
     */
    public function iAddARandomProductToMyWishList()
    {
        $productsOnPage = $this->_mink->getSession()->getPage()->findAll('css', 'li.item.products.product-item');
        $randSelection = '//ol[@class=\'products list items product-items\']//li[' . random_int(0, count($productsOnPage)) . ']//a[@title=\'Add to Wish List\']';
        var_dump($randSelection);
        $this->_mink->getSession()->getPage()->find('xpath', $randSelection)->click();
    }


    /**
     * @Then /^I should see the quantity of the test product has changed$/
     * @param $productName
     * @return bool
     * @throws ExpectationException
     */
    public function iShouldSeeTheQuantityOfTheTestProductHasChanged()
    {
        $productName = $this->testProductName;
        $xpath = "//input[@data-cart-item-id='" . $productName . "' and @title='Qty']";
        $currentQty = $this->getSession()->getPage()->find('xpath', $xpath)->getValue();
        if ($this->productQty !== $currentQty) {
            return true;
        } else {
            throw new ExpectationException('The quantity has not changed.', $this->getSession()->getDriver());
        }
    }


}