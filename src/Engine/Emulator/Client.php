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

use Facebook\WebDriver\Exception\ScriptTimeoutException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverDimension;
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
	 * @var string
	 */
	private $expectedImagesDirectory;

	/**
	 * @var string
	 */
	private $diffImagesDirectory;


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
		$this->openUrl($url);

		return $this;
	}


	/**
	 * Opens the given url.
	 *
	 * @param string $url Url to be opened.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function openUrl($url)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$result = $this->expectOpenUrl($url);

		if(!$result):
			$this->error('Failed to open url "' . $url . '"');
		endif;

		return $this;
	}
	

	/**
	 * Try to open the given url.
	 *
	 * @param string $url Url to be opened.
	 * @param int    $attempts
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function expectOpenUrl($url, $attempts = 5)
	{
		if($this->isFailed()):
			return false;
		endif;

		$result  = false;
		$attempt = 0;
		while($attempt < $attempts):
			try
			{
				$this->testSuite->getWebDriver()->get($url);
				$result = true;
				break;
			}
			catch(ScriptTimeoutException $e)
			{
				$msg = 'Unexpected ScriptTimeoutExceptionThrown and caught.' . "\n";
				if(method_exists('getResults', $e)):
					$msg .= 'Results: ' . $e->getResults() . "\n";
				endif;
				$msg .= 'Stack Trace: ' . $e->getTraceAsString();
				$this->getTestSuite()->getFileLogger()->log($msg, 'scriptTimeoutException');
			}
			catch(\Exception $e)
			{
				// Todo: Maybe display message here later ..
			}
			$attempt++;
		endwhile;

		return $result;
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
		$dimension = $this->_createMaximizedWebDriverDimension();
		// workaround for a bug in the chrome diver that the maximize command does not work like expected
		if($this->testSuite->getSuiteSettings()->getCapabilities()->getBrowserName() === 'chrome'
		   && !$this->testSuite->getWebDriver()->manage()->window()->getSize()->equals($dimension)
		):
			$this->output('Set web driver window to maximal content height "' . $dimension->getHeight() . '"');
			$this->testSuite->getWebDriver()->manage()->window()->setSize($dimension);
		endif;

		$image = $this->getExpectedImagesDirectory() . DIRECTORY_SEPARATOR . $imageName . '.png';
		$this->testSuite->getWebDriver()->takeScreenshot($image);
		$this->output('Created expected reference image "' . $image . '"');

		return $image;
	}


	/**
	 * Creates a new web driver dimension instance.
	 * The width is 1920px and the height is the maximal content size of the current window.
	 *
	 * @return WebDriverDimension
	 */
	private function _createMaximizedWebDriverDimension()
	{
		$windowContentSize = $this->testSuite->getWebDriver()->executeScript('return $("body").innerHeight();');

		return new WebDriverDimension(1920, (int)$windowContentSize);
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
		$compareImg  = $this->getExpectedImagesDirectory() . DIRECTORY_SEPARATOR . $compareImage . '.png';
		$actualImage = $this->createCompareImage('compareImage');

		$this->output("Compare\t$compareImage | $actualImage");
		
		$result = $this->imageMagick->compareImages($compareImg, $actualImage, $compareImage,
		                                            $this->getDiffImagesDirectory());

		if(!$result):
			$this->error('Actual screen does not looking equal to compare image "' . $compareImage . '"');
		endif;
		unlink($actualImage);

		return !$result;
	}


	/**
	 * Returns the path to the expected reference images directory.
	 *
	 * @return string
	 */
	public function getExpectedImagesDirectory()
	{
		if(!$this->expectedImagesDirectory):
			$this->_prepareExpectedImagesDirectory();
		endif;

		return $this->expectedImagesDirectory;
	}


	/**
	 * Returns the path to the diff images directory.
	 *
	 * @return string
	 */
	public function getDiffImagesDirectory()
	{
		if(!$this->diffImagesDirectory):
			$this->_prepareDiffImagesDirectory();
		endif;

		return $this->diffImagesDirectory;
	}


	/**
	 * Prepares the directory with the expected reference images for the test suite.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	private function _prepareExpectedImagesDirectory()
	{
		return $this->_prepareImagesDirectory('expected');
	}


	/**
	 * Prepares the directory with the diff images for the test suite.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	private function _prepareDiffImagesDirectory()
	{
		return $this->_prepareImagesDirectory('diff');
	}


	/**
	 * Prepares whether the directory with the diff images or with expected reference images.
	 *
	 * @param string $type Whether 'expected' or 'diff'.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	private function _prepareImagesDirectory($type = 'expected')
	{
		if($type !== 'expected' && $type !== 'diff')
		{
			throw new \InvalidArgumentException('Invalid $type argument, allowed types: "expected", "diff". Current value: "'
			                                    . $type . '"');
		}

		$root = $type === 'expected' ? $this->testSuite->getSuiteSettings()
		                                               ->getCompareImageDir() : $this->testSuite->getSuiteSettings()
		                                                                                        ->getDiffImageDir();

		$imagesDirectory = $root . DIRECTORY_SEPARATOR . str_replace(' ', '',
		                                                             $this->testSuite->getSuiteSettings()->getBranch())
		                   . DIRECTORY_SEPARATOR . str_replace(' ', '',
		                                                       $this->testSuite->getSuiteSettings()->getSuiteName());

		if(!is_dir($imagesDirectory)):
			$success = mkdir($imagesDirectory, 0777, true);
			if(!$success):
				throw new \RuntimeException('Unable to create the directory: "' . $imagesDirectory . '"');
			endif;
		endif;

		if($type === 'expected'):
			$this->expectedImagesDirectory = $imagesDirectory;
		else:
			$this->diffImagesDirectory = $imagesDirectory;
		endif;

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