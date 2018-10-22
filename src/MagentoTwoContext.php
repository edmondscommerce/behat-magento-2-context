<?php namespace EdmondsCommerce\BehatMagentoTwoContext;

use Behat\Behat\Tester\Exception\PendingException;

class MagentoTwoContext extends AbstractMagentoContext
{
    /**
     * @Then /^I should be on the (.*) page$/
     * @throws \Exception
     */
    public function iShouldBeOnPage($page)
    {
        switch ($page) {
            case 'checkout success':
                $expectedUrl = '/checkout/onepage/success/';
                break;
            case 'login':
                $expectedUrl = '/customer/account/login/';
                break;
            case 'account':
                $expectedUrl = '/customer/account/';
                break;
            case 'cart':
                $expectedUrl = '/checkout/cart/';
                break;
            case 'homepage':
                $expectedUrl = '/';
                break;
            default:
                throw new \Exception("Unknown page type $page");
        }

        $currentUrl = $this->getSession()->getCurrentUrl();
        $baseUrl = $this->getMinkParameter('base_url');
        $sanitizedUrl = '/' . str_replace($baseUrl, '', $currentUrl);
        if ($sanitizedUrl !== $expectedUrl) {
            throw new \Exception("Expected to be on $expectedUrl\n Actually on $sanitizedUrl");
        }
    }

    /**
     * @Given /^I go to the "([^"]*)" page$/
     * @param $page
     */
    public function iGoToThePage($page)
    {
        $this->_mink->visitPath($page);
    }


}