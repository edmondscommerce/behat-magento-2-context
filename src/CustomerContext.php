<?php namespace EdmondsCommerce\BehatMagentoTwoContext;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
class CustomerContext extends AbstractMagentoContext
{
    protected $_customerEmail;
    protected $_customerPass;
    protected $signInXPath = '//ul[@class=\'header links\']/li/a[contains(text(), \'Sign In\')]';
    protected $signOutXPath = '//ul[@class=\'header links\']/li/a[contains(text(), \'Sign Out\')]';

    public function __construct()
    {
        if(isset(self::$_magentoSetting['testCustomerEmail'])){
            $this->_customerEmail = self::$_magentoSetting['testCustomerEmail'];
        }
        if(isset(self::$_magentoSetting['testCustomerPassword'])){
            $this->_customerPass = self::$_magentoSetting['testCustomerPassword'];
        }
    }

    /**
     * @Given I am not logged in
     * @throws \Exception
     */
    public function iAmNotLoggedIn()
    {
        $this->visitPath('/');
        $this->_html->findAllOrFail('xpath', $this->signInXPath);
    }



    /**
     * @Given I should be logged in
     * @throws \Exception
     */
    public function iShouldBeLoggedIn()
    {
        $this->visitPath('/checkout/account/index');
        $this->_html->findAllOrFail('xpath', $this->signOutXPath);
    }

    /**
     * @param $email
     * @param $password
     *
     * @Given /^There is a customer with an email of ([^ ]*) and password of ([^ ]*)$/
     */
    public function thereIsACustomer($email, $password)
    {
        # log in admin
        # check if user with details exist via admin, if not create
        # check if the details are correct give exception if that is the case
        throw new PendingException();
    }

    /**
    * @Given there is a user with the following details$/
     */
    public function thereIsAUserWithTheFollowingDetails(TableNode $table)
    {
        $rows = $table->getRows();
        $this->thereIsACustomer($rows[1][0], $rows[1][1]);
    }

    /**
     * @Given /^I am logged in/
     * @throws ExpectationException
     * @throws \Exception
     */
    public function iAmLoggedIn()
    {
        $this->iAmNotLoggedIn();
        $this->iLogIn($this->_customerEmail, $this->_customerPass);
        $this->_html->findOneOrFail('xpath', $this->signOutXPath);
    }

    /**
     * @param $email
     * @param $password
     * @throws \Exception
     */
    public function iLogIn($email, $password){
        if(!isset($email) && !isset($password)){
            throw new \Exception('Please update the behat.yml file to include customerEmail and customerPass under magentoSettings.');
        }
        $this->visitPath('customer/account/index');
        $this->_mink->fillField('login[username]', $email);
        $this->_mink->fillField('login[password]', $password);
        $this->_mink->pressButton('send2');
    }


}