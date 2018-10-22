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
        if (isset(self::$_magentoSetting[self::SIMPLE_URI])) {
            $simpleURI = self::$_magentoSetting[self::SIMPLE_URI];
        } else {
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

    /**
     * @Given /^I am on the product "([^"].*)" page$/
     * @param $product
     */
    public function iAmOnTheProductPage($product)
    {
        $this->visitPath($product . '.html');
    }

    /**
     * @Given /^I select the first option on the configurable product$/
     */
    public function iSelectTheFirstOptionOnTheConfigurableProduct()
    {
        // Add javascript to add a name to both option and select field
        $script = <<<JS
(function(){
document.querySelector("div.control select").id = "select_test";
document.getElementById("select_test").children[1].id = "option_test";
})();
JS;
        $this->getSession()->executeScript($script);
        $this->getSession()->getPage()->find('css', '#select_test')->click();
        $optionText = $this->_mink->getSession()->getPage()->find('css', '#option_test')->getText();
        $this->_mink->selectOption('select_test', $optionText);
    }

    /**
     * @Given /^I update the quantity to "([^"]*)"$/
     */
    public function iUpdateTheQuantityTo($arg1)
    {
        $this->_mink->fillField('qty', $arg1);
    }



}