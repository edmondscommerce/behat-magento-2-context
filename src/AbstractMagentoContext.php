<?php
namespace EdmondsCommerce\BehatMagentoTwoContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use EdmondsCommerce\BehatFakerContext\FakerContext;
use EdmondsCommerce\BehatHtmlContext\HTMLContext;
use EdmondsCommerce\BehatHtmlContext\RedirectionContext;
use EdmondsCommerce\BehatJavascriptContext\JavascriptEventsContext;

abstract class AbstractMagentoContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    /** @var  array */
    protected static $_magentoSetting;

    /** @var array */
    protected $_contextsToInclude = [
        RedirectionContext::class      => '_redirect',
        JavascriptEventsContext::class => '_jsEvents',
        HTMLContext::class             => '_html',
        MinkContext::class             => '_mink'
    ];
    
    /**
     * @var JavascriptEventsContext
     */
    protected $_jsEvents;

    /**
     * @var RedirectionContext
     */
    protected $_redirect;

    /**
     * @var HTMLContext
     */
    protected $_html;

    /**
     * @var MinkContext
     */
    protected $_mink;


    /** @BeforeSuite
     * @param BeforeSuiteScope $scope
     *
     * @throws \Exception
     */
    public static function loadMagentoConfiguration(BeforeSuiteScope $scope)
    {
        $environment = $scope->getEnvironment();
        if (!$environment->getSuite()->hasSetting('parameters'))
        {
            throw new \Exception('You must set the parameters section of the behat.yml');
        }
        $parameters = $environment->getSuite()->getSetting('parameters');
        if (!isset($parameters['magentoSettings']))
        {
            throw new \Exception('You must include the magentoSetting in the behat.yml file');
        }
        $magentoSetting = $parameters['magentoSettings'];

        self::$_magentoSetting = $magentoSetting;
    }

    /**
     * This is used to load in the different contexts so they can be used with in the class
     *
     * @param BeforeScenarioScope $scope
     *
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $contexts = $this->_getArrayOfContexts();
        foreach ($contexts as $context => $classVar)
        {
            $this->$classVar = $environment->getContext($context);
        }
    }

    /**
     * This is used to get an array of context to include
     *
     * @return array
     */
    protected function _getArrayOfContexts()
    {
        $contexts = $this->_contextsToInclude;
        if (!is_array($contexts) || empty($contexts))
        {
            return [];
        }

        $excluded = isset($this->_contextsToExclude) ? $this->_contextsToExclude : [];
        $excluded[] = get_class($this);

        foreach ($excluded AS $contextToExclude)
        {
            if (isset($contexts[$contextToExclude]))
            {
                unset($contexts[$contextToExclude]);
            }
        }

        return $contexts;
    }

    public function iWaitForMagento2AjaxToFinish()
    {
        $this->getSession()->wait(10000, '(0 === jQuery.active)');
        $this->getSession()->wait(1000);
    }

    public function iWaitForMagento2LoaderToFinish()
    {
        $this->getSession()->wait(10000, '("none" == document.querySelector(".loading-mask").style.display)');
        $this->getSession()->wait(1000);
    }
}