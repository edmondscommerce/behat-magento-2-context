<?php
namespace EdmondsCommerce\BehatMagentoTwoContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;

class CheckoutContext extends AbstractMagentoContext
{
    const CHECKOUT_SUCCESS_PAGE_TITLE_SETTING = 'successPageTitle';
    const CHECKOUT_SUCESS_PAGE_TITLE_DEFAULT = 'Success Page';
    /**
     * @Then I go to the checkout
     */
    public function iGoToTheCheckout()
    {
        $this->visitPath('/checkout/');
    }

    /**
     * @Then I fill in a guest checkout shipping
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillInAGuestCheckoutShipping()
    {
        $this->iWaitForMagento2AjaxToFinish();
        $this->getSession()->getPage()->find('css','#customer-email')->setValue('behatguest@example.com');
        $shippingForm = $this->getSession()->getPage()->find('css', '#co-shipping-form');

        $inputs = $shippingForm->findAll('css', 'input');

        foreach ($inputs as $input) {
            switch ($input->getAttribute('name')) {
                case 'username':
                    $value = 'behat@example.com';
                    break;
                case 'firstname':
                    $value = 'Behat';
                    break;
                case 'lastname':
                    $value = 'Customer';
                    break;
                case 'billing[telephone]':
                    $value = '0123456789';
                    break;
                case 'street[0]':
                    $value = '123 Main Street';
                    break;
                case 'city':
                    $value = 'Leeds';
                    break;
                case 'postcode':
                    $value = 'LS1 2AB';
                    break;
                case 'telephone':
                    $value = '01257898789';
                    break;
                default:
                    $value = false;
            }
            if ($value === false) {
                continue;
            }
            $input->setValue($value);
        }
        $this->iWaitForMagento2AjaxToFinish();
    }




    /**
     * @Then I submit a guest checkout shipping
     */
    public function iSubmitAGuestCheckoutShipping()
    {
        $this->iWaitForMagento2AjaxToFinish();
        $xpath = '//form[@id=\'co-shipping-method-form\']';
        $this->getSession()->getPage()->find('xpath',$xpath)->submit();
        $this->iWaitForMagento2AjaxToFinish();
    }

    /**
     * @Then I should see the order success page
     * @throws \Behat\Mink\Exception\ResponseTextException
     * @throws \Exception
     */
    public function iShouldSeeTheOrderSuccess()
    {
        $this->assertSession()->pageTextContains('Your order # is');

        $elements = $this->getSession()->getPage()->findAll('css', '.checkout-success');

        if($elements  < 0) {
            throw new \Exception('The success page is not showing');
        }

    }

    /**
     * @Then /^I should see the order cancelled page/
     */
    public function iShouldSeeTheOrderCancelledPage()
    {
        $this->getSession()->getPage()->find('xpath', '//span[contains(text(), \'Your order has been succesfully cancelled.\')]');
    }


    private function getSucessPageTitle()
    {
        if (isset(self::$_magentoSetting[self::CHECKOUT_SUCCESS_PAGE_TITLE_SETTING]))
        {
            return self::$_magentoSetting[self::CHECKOUT_SUCCESS_PAGE_TITLE_SETTING];
        }
        return self::CHECKOUT_SUCESS_PAGE_TITLE_DEFAULT;
    }

}