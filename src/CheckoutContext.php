<?php
namespace EdmondsCommerce\BehatMagentoTwoContext;

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
     */
    public function iFillInAGuestCheckoutShipping()
    {
        $this->getSession()->wait(10000);
        $this->getSession()->getPage()->find('css','#customer-email')->setValue('behatguest@example.com');
        $shippingForm = $this->getSession()->getPage()->find('css', '#co-shipping-form');

        $inputs      = $shippingForm->findAll('css', 'input');

        foreach ($inputs as $input) {
            switch ($input->getAttribute('name')) {
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

        $this->getSession()->getPage()->selectFieldOption('country_id', 'GB');
        $this->getSession()->wait(1000);
    }

    /**
     * @Then I submit a guest checkout shipping
     */
    public function iSubmitAGuestCheckoutShipping()
    {
        $this->getSession()->wait(10000);
        $this->iWaitForMagento2AjaxToFinish();
        $this->_html->iClickOnTheFirstVisibleText('Next');
        $this->iWaitForMagento2AjaxToFinish();
    }

    /**
     * @Then I should see the order success page
     */
    public function iShouldSeeTheOrderSuccess()
    {
        $title = trim($this->getSession()->getPage()->find('css', 'title')->getText());
        if($title != $this->getSucessPageTitle()) {
            throw new \Exception(sprintf('Not got the expected success page title Expected: %s GOT: %s',$this->getSucessPageTitle(), $title));
        }

        $elements = $this->getSession()->getPage()->findAll('css', '.checkout-success');

        if($elements  < 0) {
            throw new \Exception('The success page is not showing');
        }

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