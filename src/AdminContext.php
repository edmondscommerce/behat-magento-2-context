<?php

namespace EdmondsCommerce\BehatMagentoTwoContext;

use Behat\Behat\Tester\Exception\PendingException;

class AdminContext extends AbstractMagentoContext
{
    protected $_amLoggedIn = false;

    const ADMIN_URI = 'adminUri';
    const USER_NAME = 'userName';
    const PASSWORD = 'password';
    const CREATED_EMAIL = 'createdEmail';
    const CREATED_PASSWORD = 'createdPassword';

    private $_userName = '';
    private $_password = '';
    private $_adminUri = '';
    private $_createdEmail = '';

    public function __construct()
    {
        if (isset(self::$_magentoSetting[self::ADMIN_URI])) {
            $this->_adminUri = self::$_magentoSetting[self::ADMIN_URI];
        }

        if (isset(self::$_magentoSetting[self::USER_NAME])) {
            $this->_userName = self::$_magentoSetting[self::USER_NAME];
        }

        if (isset(self::$_magentoSetting[self::PASSWORD])) {
            $this->_password = self::$_magentoSetting[self::PASSWORD];
        }

        if (isset(self::$_magentoSetting[self::CREATED_EMAIL])) {
            $this->_createdEmail = self::$_magentoSetting[self::CREATED_EMAIL];
        }

    }

    /**
     * @Given I log into the admin
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     * @throws \Behat\Mink\Exception\ExpectationException
     * @throws \Exception
     */
    public function iLogInToTheAdmin()
    {
        if (is_null($this->_userName) || is_null($this->_password)) {
            throw new \Exception('You must create the admin user first');
        }
        $this->visitPath($this->_adminUri);
        $this->getSession()->getPage()->fillField('username', $this->_userName);
        $this->getSession()->getPage()->fillField('login', $this->_password);
        $this->_html->iClickOnTheElement('button.action-login');
        $this->_jsEvents->iWaitForDocumentReady();
        $this->assertSession()->responseContains('Dashboard');
        $this->_amLoggedIn = true;
    }

    /**
     * @When I go to the orders page
     * @throws \Exception
     */
    public function iGoToTheOrdersPage()
    {
        $found = '';

        if ($this->_amLoggedIn === false) {
            $this->iLogInToTheAdmin();
        }

        $orderLinkOptionElement = $this->getSession()->getPage()->find('css', '.item-sales-operation ul .item-sales-order a');

        $found = $orderLinkOptionElement->getAttribute('href');

        if (empty($found)) {
            throw new \Exception('Could not find the orders link');
        }

        $this->visitPath($found);
        $this->_jsEvents->iWaitForDocumentReady();
        $this->assertSession()->responseContains('Orders');
        $this->iWaitForMagento2AjaxToFinish();
        $this->getSession()->wait(5000);
    }

    /**
     * @When On the order page I click on order :order
     */
    public function onTheOrderPageIClickOnOrder($orderRef)
    {
        $tableRows = $this->getSession()
            ->getPage()->findAll('css', 'table.data-grid tbody tr');
        $linkHref = '';

        foreach ($tableRows as $row) {
            $html = $row->getHtml();

            if (stripos($html, $orderRef) > 0) {
                $linkElement = $row->find('css', 'a.action-menu-item');
                if (stripos($linkElement->getAttribute('href'), 'view/order_id') > 0) {
                    $linkHref = $linkElement->getAttribute('href');
                    break;
                }
            }
        }
        if (empty($linkHref)) {
            throw new \Exception('Could not find the order');
        }

        $this->visitPath($linkHref);
        $this->_jsEvents->iWaitForDocumentReady();
        $this->assertSession()->pageTextContains('# ' . $orderRef);
    }

    /**
     * @When I invoice the order
     */
    public function iInvoiceTheOrder()
    {
        $this->getSession()->getPage()->find('css', '#order_invoice')->click();
        $this->_jsEvents->iWaitForDocumentReady();
        $this->assertSession()->pageTextContains('New Invoice');
        //.submit-button
        $this->getSession()->getPage()->find('css', '.submit-button')->click();
        $this->getSession()->wait(10000);
    }

    /**
     * @When I click on the order invoice tab
     */
    public function iClickOnTheOrderInvoiceTab()
    {
        $this->getSession()->getPage()->find('css', '#sales_order_view_tabs_order_invoices')->click();
        $this->iWaitForMagento2AjaxToFinish();
    }

    /**
     * @When I click on the credit memo tab
     */
    public function iClickOnTheCreditMemoTab()
    {
        $this->getSession()->getPage()->find('css', '#sales_order_view_tabs_order_creditmemos')->click();
        $this->iWaitForMagento2AjaxToFinish();
    }

    /**
     * @When I go to the first invoice in an order
     */
    public function iGoToTheFirstInvoiceInAnOrder()
    {
        $this->iClickOnTheOrderInvoiceTab();
        $this->getSession()->wait(5000);

        $links = $this->getSession()
            ->getPage()->findAll('css', '#sales_order_view_tabs_order_invoices_content .data-grid tbody tr td a');

        if (count($links) > 0) {
            $this->visitPath($links[0]->getAttribute('href'));
            $this->_jsEvents->iWaitForDocumentReady();
            $this->assertSession()->pageTextContains('Invoice Totals');
        } else {
            throw new \Exception('Could not see a invoice');
        }
    }

    /**
     * @Then /^(?:|I )should see a credit memo/
     */
    public function iShouldSeeCreditMemos()
    {
        $this->iClickOnTheCreditMemoTab();
        $this->getSession()->wait(5000);

        $rows = $this->getSession()
            ->getPage()->findAll('css', '#sales_order_view_tabs_order_creditmemos_content .data-grid tbody tr');

        if (empty($rows)) {
            throw new \Exception('Could not see any credit memos');
        }
    }

    /**
     * @When /^(?:|I )create an credit memo against an invoice/
     */
    public function performCreditMemoAgainstAnInvoice()
    {
        $this->assertSession()->pageTextContains('Invoice Totals');
        $this->getSession()->getPage()->find('css', '#capture')->click();
        $this->_jsEvents->iWaitForDocumentReady();
        $this->getSession()->getPage()->find('css', 'button[title="Refund"]')->click();
        $this->_jsEvents->iWaitForDocumentReady();
    }


    /**
     * @Given /^I search for the created customer email$/
     * @param $customerEmail
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function iSearchForTheCustomerName()
    {
        $this->_jsEvents->iWaitForAjaxToFinish();
        $this->_html->waitformilliseconds(1000);
        $this->getSession()->getPage()->find('xpath', '//button[contains(text(),\'Filters\')]')->click();
        $this->_mink->fillField('email', $this->_createdEmail);
        $this->getSession()->getPage()->find('xpath', '//div[@class=\'admin__footer-main-actions\']//button[2]')->click();
        $this->_html->findAllOrFail('xpath', '//div[contains(text(),\'' . $this->_createdEmail . '\')]');
        $this->_jsEvents->iWaitForAjaxToFinish();
    }

    /**
     * @Given /^I am on the customers page$/
     */
    public function iAmOnTheCustomersPage()
    {
        $this->visitPath('/' . $this->_adminUri . 'customer/index');
        $this->_jsEvents->iWaitForDocumentReady();
    }

    /**
     * @When /^I click on the first customers edit button$/
     */
    public function iClickOnTheFirstCustomersEditButton()
    {
        $this->getSession()->getPage()->find('xpath', '(//a[@class=\'action-menu-item\'])[0]')->click();
    }

}