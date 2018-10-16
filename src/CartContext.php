<?php namespace EdmondsCommerce\BehatMagentoTwoContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Exception\ExpectationException;

class CartContext extends AbstractMagentoContext implements Context, SnippetAcceptingContext
{
    private $productQty;

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
    public function iClickOnTheAddToCartButton(){

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
        $text = $this->getSession()->getPage()->getText();
        $xpath = '//*[contains(text(), "Clear Shopping Cart") or contains(text(), "Clear") or contains(text(), "You have no items in your shopping cart.")]';
        $search = $this->getSession()->getPage()->find('xpath', $xpath);
        if ($search === null) {
            throw new ExpectationException('Could not find the clear shopping cart button', $this->getSession()->getDriver());
        }
        $search->click();
    }

    /**
     * @Given /^I have an empty cart$/
     * @throws \Exception
     */
    public function haveAnEmptyCart()
    {
        $this->visitPath('/checkout/cart');
        // find clear shopping cart button
        if($this->getSession()->getPage()->find('xpath', '//button[@id=\'empty_cart_button\']')){
            $this->clickOnClearShoppingCartButton();
        }
    }

    /**
     * @Given /^I change the quantity of the "([^"]*)" in the cart to "([^"]*)"$/
     * @param $productName
     * @param $value
     */
    public function iUpdateTheQuantityOfTheProductInTheCheckoutTo($productName, $value)
    {
        $xpath = "a[text($productName)]../../../input[name*=\"qty\"][type=\"text\"]";
        // Set original quantity before changing for comparison
        $this->productQty = $this->getSession()->getPage()->find('css', $xpath)->getValue();

        // Change the quantity input value
        $this->getSession()->getPage()->find('xpath', $xpath)->setValue($value);
        $this->getSession()->getPage()->find('xpath', '//a[contains(text(), "Update Shopping Cart")]')->click();
    }

    /**
     * @Then /^I should see the quantity of the "([^"]*)" has changed$/
     * @param $productName
     * @return bool
     * @throws ExpectationException
     */
    public function iShouldSeeTheQuantityOfTheProductHasChanged($productName)
    {
        $xpath = "a[text($productName)]../../../input[name*=\"qty\"][type=\"text\"]";
        $currentQty = $this->getSession()->getPage()->find('css', $xpath)->getValue();
        if($this->productQty !== $currentQty){
            return true;
        }else{
            throw new ExpectationException('The quantity has not changed.', $this->getSession()->getDriver());
        }
    }

    /**
     * @Given I add a different product
     */
    public function iAddADifferentProduct(){
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
        $randSelection = '//ol[@class=\'products list items product-items\']//li[' . random_int(0,count($productsOnPage)) . ']//a[@title=\'Add to Wish List\']';
        var_dump($randSelection);
        $this->_mink->getSession()->getPage()->find('xpath', $randSelection)->click();
    }


}