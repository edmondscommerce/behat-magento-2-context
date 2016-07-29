<?php
namespace EdmondsCommerce\BehatMagentoTwoContext;

class ProductContext extends AbstractMagentoContext
{
    const CONFIGURABLE_URI = 'configurableUri';
    const SIMPLE_URI = 'simpleUri';
    const BUNDLE_URI = 'bundleUri';
    const CATEGORY_URI = 'categoryUri';
    const GROUPED_URI = 'groupedUri';

    /**
     * @Given I am on a simple product page remotely
     */
    public function iAmOnASimpleProductPage()
    {
        if (isset(self::$_magentoSetting[self::SIMPLE_URI]))
        {
            $simpleURI = self::$_magentoSetting[self::SIMPLE_URI];
        }
        else
        {
            $simpleURI = 'fusion-backpack.html';
        }
        $this->visitPath('/' . $simpleURI);
    }

    /**
     * Add the product on the product page to cart, just clicks the add to cart element
     * @Then /^I add to cart remotely$/
     * @When I click the Add To Cart button remotely
     */
    public function iAddToCart()
    {
        $this->_html->iClickOnTheFirstVisibleText('Add to Cart');
        $this->iWaitForMagento2AjaxToFinish();
    }
}