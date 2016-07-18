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

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverSelect;
use GXSelenium\Engine\Provider\ElementProvider;
use GXSelenium\Engine\Provider\Traits\ClickProviderTrait;
use GXSelenium\Engine\Provider\Traits\ElementProviderTrait;
use GXSelenium\Engine\Provider\Traits\InspectionProviderTrait;
use GXSelenium\Engine\Provider\Traits\MouseTrait;
use GXSelenium\Engine\Provider\Traits\SelectingProviderTrait;
use GXSelenium\Engine\Provider\Traits\TypingProviderTrait;
use GXSelenium\Engine\Provider\Traits\VerificationTrait;
use GXSelenium\Engine\Provider\Traits\WaitProviderTrait;
use GXSelenium\Engine\TestSuite;
use ImageMagick\ImageMagick;

/**
 * Class Client
 * @package GXSelenium\Engine\Emulator
 */
class Client
{
	use ClickProviderTrait, InspectionProviderTrait, SelectingProviderTrait, TypingProviderTrait, MouseTrait, WaitProviderTrait, VerificationTrait, ElementProviderTrait;

	/**
	 * @var TestSuite
	 */
	private $testSuite;

	/**
	 * @var ElementProvider
	 */
	private $elementProvider;


	/**
	 * @var bool
	 */
	private $failed = false;

	/**
	 * @var ImageMagick
	 */
	private $imageMagick;


	/**
	 * Initialize the client emulator.
	 *
	 * @param TestSuite       $testSuite
	 * @param ElementProvider $elementProvider
	 */
	public function __construct(TestSuite $testSuite, ElementProvider $elementProvider)
	{
		$this->testSuite       = $testSuite;
		$this->elementProvider = $elementProvider;
		$this->imageMagick     = new ImageMagick();
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
		$url = $this->testSuite->getSuiteSettings()->getBaseUrl() . '/' . $this->testSuite->getSuiteSettings()
		                                                                                  ->getWebApp() . '/';
		foreach($pathArray as $path):
			$url .= $path . '/';
		endforeach;
		$url = rtrim($url, '/');
		$this->output('Open url|' . "\t" . $url);
		$this->testSuite->getWebDriver()->get($url);

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
		$this->testSuite->getWebDriver()->executeScript('javascript:window.scrollTo(' . $xPos . ', ' . $yPos . ')');

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
		if($this->isFailed()):
			return $this;
		endif;
		$xOffset = $this->testSuite->getSuiteSettings()->getScrollXOffset();
		$yOffset = $this->testSuite->getSuiteSettings()->getScrollYOffset();

		$xPos = $element->getLocation()->getX() - $xOffset;
		$yPos = $element->getLocation()->getY() - $yOffset;

		return $this->scrollTo($xPos, $yPos);
	}


	/**
	 * Logs an error and do a screen shot of the current screen.
	 *
	 * @param string $message Message to log.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function error($message)
	{
		$this->testSuite->getSuiteSettings()->getCurrentTestCase()->_error($message);

		return $this;
	}


	/**
	 * Logs an exception error and do a screen shot of the current screen.
	 *
	 * @param string     $message Message to log.
	 * @param \Exception $e
	 *
	 * @return $this|\GXSelenium\Engine\Emulator\Client Same instance for chained method calls.
	 */
	public function exceptionError($message, \Exception $e)
	{
		$this->testSuite->getSuiteSettings()->getCurrentTestCase()->_exceptionError($message, $e);

		return $this;
	}


	/**
	 * Displays a message on the console.
	 * Delegates to the test case output method.
	 *
	 * @see TestCase::output
	 *
	 * @param string $message Message to display on the console.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function output($message)
	{
		$this->testSuite->getSuiteSettings()->getCurrentTestCase()->output($message);

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
		if($this->failed):
			return $this;
		endif;

		$this->output('Client deactivated ...');
		$this->failed = true;

		return $this;
	}
	
	
	/**
	 * Creates a new PNG image of the current screen in the compare image directory of the suite settings.
	 *
	 * @param $imageName
	 *
	 * @return string
	 */
	public function createCompareImage($imageName)
	{
		$compareImageDir = $this->testSuite->getSuiteSettings()->getCompareImageDir() . DIRECTORY_SEPARATOR;
		$image           = $compareImageDir . $imageName . '.png';
		$this->testSuite->getWebDriver()->takeScreenshot($image);

		return $image;
	}
	

	/**
	 * Compares an image with a screenshot of the currently displayed screen.
	 * When they are different, a gif of both images will be created.
	 * Returns true when the images looking equal and false otherwise.
	 *
	 * @param $compareImage
	 *
	 * @return bool
	 */
	public function compareWithVerificationImage($compareImage)
	{
		$compareImg = $this->testSuite->getSuiteSettings()->getCompareImageDir() . DIRECTORY_SEPARATOR . $compareImage
		              . '.png';
		$this->output("Create\tCompare Image");
		$actualImage  = $this->createCompareImage('compareImage');
		$this->output("Compare\t$compareImage | $actualImage");
		$result       = $this->imageMagick->createDiffGifOnDifferences($actualImage, $compareImg,
		                                                               $compareImage . 'Diff',
		                                                               $this->testSuite->getSuiteSettings()
		                                                                               ->getDiffImageDir());

		unlink($actualImage); #

		return !$result;
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
		return $this->testSuite->getWebDriver();
	}


	/**
	 * Resets the internal failed property of the client.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function reset()
	{
		if($this->failed):
			$this->output("\n" . 'Client reset ...');
		endif;
		$this->failed = false;

		$this->elementProvider->reset();

		return $this;
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
		if(!$this->failed && $elementProviderFailed):
			$this->failed();
		endif;

		return $this->failed;
	}


	/**
	 * Returns the test suite instance.
	 *
	 * @return TestSuite
	 */
	public function getTestSuite()
	{
		return $this->testSuite;
	}
}