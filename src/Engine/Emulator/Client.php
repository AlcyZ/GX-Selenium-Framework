<?php

/**
 * Copyright (c) 2015 Tobias Schindler
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace GXSelenium\Engine\Emulator;

use Facebook\WebDriver\JavaScriptExecutor;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverSelect;
use GXSelenium\Engine\Provider\ElementProvider;
use GXSelenium\Engine\Provider\Traits\ClickProviderTrait;
use GXSelenium\Engine\Provider\Traits\InspectionProviderTrait;
use GXSelenium\Engine\Provider\Traits\MouseTrait;
use GXSelenium\Engine\Provider\Traits\SelectingProviderTrait;
use GXSelenium\Engine\Provider\Traits\TypingProviderTrait;
use GXSelenium\Engine\TestSuite;

/**
 * Class Client
 * @package GXSelenium\Engine\Emulator
 *          
 * @Todo Maybe refactor instantiation in selenium factory .. are multiple instances required (if not, refactor!)
 */
class Client
{
    use ClickProviderTrait, InspectionProviderTrait, SelectingProviderTrait, TypingProviderTrait, MouseTrait;

    /**
     * @var TestSuite
     */
    private $testSuite;

    /**
     * @var WebDriver|JavaScriptExecutor
     */
    private $webDriver;

    /**
     * @var ElementProvider
     */
    private $elementProvider;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var bool
     */
    private $failed = false;


    /**
     * Initialize the client emulator.
     *
     * @param TestSuite $testSuite
     * @param ElementProvider $elementProvider
     */
    public function __construct(TestSuite $testSuite, ElementProvider $elementProvider)
    {
        $this->testSuite = $testSuite;
        $this->webDriver = $this->testSuite->getWebDriver();
        $this->elementProvider = $elementProvider;
        $this->baseUrl =
            $this->testSuite->getSuiteSettings()->getBaseUrl() . '/' . $this->testSuite->getSuiteSettings()
                ->getWebApp();
    }


    /**
     * Open the base url with the web app path (given from constructor argument).
     * When the second argument isset, a sub url is opened.
     *
     * @param array $pathArray Array which elements are the sub paths.
     *
     * @return $this|Client Same instance for chained method calls.
     */
    public function openBaseUrl(array $pathArray = array())
    {
        $url = $this->baseUrl . '/';
        foreach ($pathArray as $path):
            $url .= $path . '/';
        endforeach;
        $url = rtrim($url, '/');
        $this->webDriver->get($url);

        return $this;
    }


    /**
     * Scroll to a position.
     *
     * @param int $xPos X-position to scroll.
     * @param int $yPos Y-position to scroll.
     *
     * @return $this Same instance for chained method calls.
     */
    public function scrollTo($xPos = 0, $yPos = 0)
    {
        $this->webDriver->executeScript('javascript:window.scrollTo(' . $xPos . ', ' . $yPos . ')');

        return $this;
    }


    /**
     * Scroll to an element.
     *
     * @param WebDriverElement $element The element to that the client scroll.
     *
     * @Todo Add settings - e.g.: scrollElementOffset (Maybe the use the suite settings or create settings for test
     *       cases)
     *
     * @return $this|Client Same instance for chained method calls.
     */
    public function scrollToElement(WebDriverElement $element)
    {
        $xOffset = $this->testSuite->getSuiteSettings()->getScrollXOffset();
        $yOffset = $this->testSuite->getSuiteSettings()->getScrollYOffset();

        $xPos = $element->getLocation()->getX() - $xOffset;
        $yPos = $element->getLocation()->getY() - $yOffset;

        return $this->scrollTo($xPos, $yPos);
    }


    /**
     * Wait the specified amount of time until the case will continue.
     *
     * @param string $expectedUrlSnippet Snippet of url to match before continue the case.
     * @param int $waitTimeout Amount of seconds to wait before the case fail.
     *
     * @return $this|Client Same instance for chained method calls.
     *
     * @throws \Exception Look at WebDriverWait::until() for detailed information.
     * @throws \Facebook\WebDriver\Exception\TimeOutException Look at WebDriverWait::until() for detailed information.
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException Look at WebDriverWait::until() for detailed
     *                                                              information.
     * @codeCoverageIgnore
     */
    public function waitForPageLoaded($expectedUrlSnippet, $waitTimeout)
    {
        $this->webDriver->wait($waitTimeout)->until(function ($webDriver) use ($expectedUrlSnippet) {
            /** @var RemoteWebDriver $webDriver */
            return (strpos($webDriver->getCurrentURL(), $expectedUrlSnippet)) ? true : false;
        });

        return $this;
    }


    /**
     * Sets the failed property to true.
     *
     * @return $this|Client Same instance for chained method calls.
     */
    public function failed()
    {
	    $this->testSuite->setFailed(true);
        if ($this->failed):
            return $this;
        endif;

        echo "Client deactivated ..\n";
        $this->failed = true;

        return $this;
    }


    /**
     * Returns the element provider which is required for the trait methods.
     *
     * @return ElementProvider
     */
    public function getElementProvider()
    {
        return $this->elementProvider;
    }


    /**
     * Returns if the element provider is failed or not.
     *
     * @return bool
     */
    public function isElementProviderFailed()
    {
        return $this->elementProvider->isFailed();
    }


    /**
     * Returns the web driver instance.
     *
     * @return RemoteWebDriver
     */
    public function getWebDriver()
    {
        return $this->webDriver;
    }


    /**
     * Implementation of abstract method signature from trait.
     *
     * @param WebDriverElement $element
     *
     * @return WebDriverSelect
     * @codeCoverageIgnore
     */
    protected function _createWebDriverSelect(WebDriverElement $element)
    {
        return new WebDriverSelect($element);
    }

    /**
     * Returns true when the test case is failed.
     *
     * @return bool
     */
    public function isFailed()
    {
        $elementProviderFailed = $this->isElementProviderFailed();
        if (!$this->failed && $elementProviderFailed):
            $this->failed();
        endif;

        return $this->failed;
    }
}