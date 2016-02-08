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

namespace GXSelenium\Engine\Provider;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Internal\WebDriverLocatable;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverSearchContext;
use GXSelenium\Engine\Logger\FileLogger;
use GXSelenium\Engine\NullObjects\WebDriverElementNull;
use GXSelenium\Engine\TestSuite;

/**
 * Class ElementProvider
 * @package GXSelenium\Engine\Provider
 */
class ElementProvider
{
	/**
	 * @var TestSuite
	 */
	private $testSuite;

	/**
	 * @var WebDriver
	 */
	private $webDriver;

	/**
	 * @var bool
	 */
	private $failed = false;

	/**
	 * @var FileLogger
	 */
	private $fileLogger;


	/**
	 * Initialize the element provider.
	 *
	 * @param TestSuite  $testSuite
	 */
	public function __construct(TestSuite $testSuite)
	{
		$this->testSuite  = $testSuite;
		$this->webDriver  = $testSuite->getWebDriver();
		$this->fileLogger = $testSuite->getFileLogger();
	}


	/**
	 * Returns a web driver element by the given id.
	 *
	 * @param string           $id      Id value.
	 * @param WebDriverElement $element Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function byId($id, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 2));

		return ($element) ? $this->_by($by, $id, $element) : $this->_by($by, $id, $this->webDriver);
	}


	/**
	 * Try to return a web driver element by the given id.
	 *
	 * @param string           $id      Id value.
	 * @param WebDriverElement $element Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function tryById($id, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 5));

		return ($element) ? $this->_tryBy($by, $id, $element) : $this->_tryBy($by, $id, $this->webDriver);
	}


	/**
	 * Returns an element by the given name attribute value.
	 *
	 * @param string                $name Name html attribute.
	 * @param WebDriverElement|null $element
	 *
	 * @return WebDriverElement
	 */
	public function byName($name, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 2));

		return ($element) ? $this->_by($by, $name, $element) : $this->_by($by, $name, $this->webDriver);
	}


	/**
	 * Try to return an element by the given name attribute value.
	 *
	 * @param string                $name Name html attribute.
	 * @param WebDriverElement|null $element
	 *
	 * @return WebDriverElement
	 */
	public function tryByName($name, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 5));

		return ($element) ? $this->_tryBy($by, $name, $element) : $this->_tryBy($by, $name, $this->webDriver);
	}


	/**
	 * Returns an array with elements by the given name attribute value.
	 *
	 * @param string                $name Name html attribute.
	 * @param WebDriverElement|null $element
	 *
	 * @return WebDriverElement[]|WebDriverLocatable[]|RemoteWebElement[]
	 */
	public function arrayByName($name, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 7));

		return ($element) ? $this->_arrayBy($by, $name, $element) : $this->_arrayBy($by, $name, $this->webDriver);
	}


	/**
	 * Returns a web driver element by the given class name.
	 *
	 * @param string           $className Class name value.
	 * @param WebDriverElement $element   Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function byClassName($className, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 2));

		return ($element) ? $this->_by($by, $className, $element) : $this->_by($by,
		                                                                       $className,
		                                                                       $this->webDriver);
	}


	/**
	 * Try to return a web driver element by the given class name.
	 *
	 * @param string           $className Class name value.
	 * @param WebDriverElement $element   Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function tryByClassName($className, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 5));

		return ($element) ? $this->_tryBy($by, $className, $element) : $this->_tryBy($by,
		                                                                             $className,
		                                                                             $this->webDriver);
	}


	/**
	 * Returns an array with web driver elements by the given class name.
	 *
	 * @param string           $className Class name value.
	 * @param WebDriverElement $element   Expected value of searching type.
	 *
	 * @return WebDriverElement[]|WebDriverLocatable[]|RemoteWebElement[] Expected web driver element.
	 */
	public function arrayByClassName($className, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 7));

		return ($element) ? $this->_arrayBy($by, $className, $element) : $this->_arrayBy($by,
		                                                                                 $className,
		                                                                                 $this->webDriver);
	}


	/**
	 * Returns a web driver element by the given link text.
	 *
	 * @param string           $linkText Link text value.
	 * @param WebDriverElement $element  Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function byLinkText($linkText, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 2));

		return ($element) ? $this->_by($by, $linkText, $element) : $this->_by($by,
		                                                                      $linkText,
		                                                                      $this->webDriver);
	}


	/**
	 * Try to return a web driver element by the given link text.
	 *
	 * @param string           $linkText Link text value.
	 * @param WebDriverElement $element  Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function tryByLinkText($linkText, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 5));

		return ($element) ? $this->_tryBy($by, $linkText, $element) : $this->_tryBy($by,
		                                                                            $linkText,
		                                                                            $this->webDriver);
	}


	/**
	 * Returns an array with web driver elements by the given link text.
	 *
	 * @param string           $linkText Link text value.
	 * @param WebDriverElement $element  Expected value of searching type.
	 *
	 * @return WebDriverElement[]|WebDriverLocatable[]|RemoteWebElement[] Expected web driver element.
	 */
	public function arrayByLinkText($linkText, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 7));

		return ($element) ? $this->_arrayBy($by, $linkText, $element) : $this->_arrayBy($by,
		                                                                                $linkText,
		                                                                                $this->webDriver);
	}


	/**
	 * Returns a web driver element by the given partial link text.
	 *
	 * @param string           $partialLinkText Partial link text value.
	 * @param WebDriverElement $element         Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function byPartialLinkText($partialLinkText, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 2));

		return ($element) ? $this->_by($by, $partialLinkText, $element) : $this->_by($by,
		                                                                             $partialLinkText,
		                                                                             $this->webDriver);
	}


	/**
	 * Try to return a web driver element by the given partial link text.
	 *
	 * @param string           $partialLinkText Partial link text value.
	 * @param WebDriverElement $element         Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function tryByPartialLinkText($partialLinkText, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 5));

		return ($element) ? $this->_tryBy($by, $partialLinkText, $element) : $this->_tryBy($by,
		                                                                                   $partialLinkText,
		                                                                                   $this->webDriver);
	}


	/**
	 * Returns an array with web driver elements by the given partial link text.
	 *
	 * @param string           $partialLinkText Partial link text value.
	 * @param WebDriverElement $element         Expected value of searching type.
	 *
	 * @return WebDriverElement[]|WebDriverLocatable[]|RemoteWebElement[] Expected web driver element.
	 */
	public function arrayByPartialLinkText($partialLinkText, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 7));

		return ($element) ? $this->_arrayBy($by, $partialLinkText, $element) : $this->_arrayBy($by,
		                                                                                       $partialLinkText,
		                                                                                       $this->webDriver);
	}


	/**
	 * Returns a web driver element by the given tag name.
	 *
	 * @param string           $tagName Tag name value.
	 * @param WebDriverElement $element Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function byTagName($tagName, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 2));

		return ($element) ? $this->_by($by, $tagName, $element) : $this->_by($by,
		                                                                     $tagName,
		                                                                     $this->webDriver);
	}


	/**
	 * Try to return a web driver element by the given tag name.
	 *
	 * @param string           $tagName Tag name value.
	 * @param WebDriverElement $element Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function tryByTagName($tagName, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 5));

		return ($element) ? $this->_tryBy($by, $tagName, $element) : $this->_tryBy($by,
		                                                                           $tagName,
		                                                                           $this->webDriver);
	}


	/**
	 * Returns an array with web driver elements by the given xPath value.
	 *
	 * @param string           $tagName Tag name value.
	 * @param WebDriverElement $element Expected value of searching type.
	 *
	 * @return WebDriverElement[]|WebDriverLocatable[]|RemoteWebElement[] Expected web driver element.
	 */
	public function arrayByTagName($tagName, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 7));

		return ($element) ? $this->_arrayBy($by, $tagName, $element) : $this->_arrayBy($by,
		                                                                               $tagName,
		                                                                               $this->webDriver);
	}


	/**
	 * Returns a web driver element by the given css selector.
	 *
	 * @param string           $cssSelector Css selector value.
	 * @param WebDriverElement $element     Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function byCssSelector($cssSelector, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 2));

		return ($element) ? $this->_by($by, $cssSelector, $element) : $this->_by($by,
		                                                                         $cssSelector,
		                                                                         $this->webDriver);
	}


	/**
	 * Try to return a web driver element by the given css selector.
	 *
	 * @param string           $cssSelector Css selector value.
	 * @param WebDriverElement $element     Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function tryByCssSelector($cssSelector, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 5));

		return ($element) ? $this->_tryBy($by, $cssSelector, $element) : $this->_tryBy($by,
		                                                                               $cssSelector,
		                                                                               $this->webDriver);
	}


	/**
	 * Returns an array with web driver elements by the given css selector.
	 *
	 * @param string           $cssSelector Css selector value.
	 * @param WebDriverElement $element     Expected value of searching type.
	 *
	 * @return WebDriverElement[]|WebDriverLocatable[]|RemoteWebElement[] Expected web driver element.
	 */
	public function arrayByCssSelector($cssSelector, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 7));

		return ($element) ? $this->_arrayBy($by, $cssSelector, $element) : $this->_arrayBy($by,
		                                                                                   $cssSelector,
		                                                                                   $this->webDriver);
	}


	/**
	 * Returns a web driver element by the given xPath value.
	 *
	 * @param string           $xPath   xPath value.
	 * @param WebDriverElement $element Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function byXpath($xPath, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 2));

		return ($element) ? $this->_by($by, $xPath, $element) : $this->_by($by,
		                                                                   $xPath,
		                                                                   $this->webDriver);
	}


	/**
	 * Try to return a web driver element by the given xPath value.
	 *
	 * @param string           $xPath   xPath value.
	 * @param WebDriverElement $element Expected value of searching type.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	public function tryByXpath($xPath, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 5));

		return ($element) ? $this->_tryBy($by, $xPath, $element) : $this->_tryBy($by,
		                                                                         $xPath,
		                                                                         $this->webDriver);
	}


	/**
	 * Returns an array with web driver elements by the given xPath value.
	 *
	 * @param string           $xPath   xPath value.
	 * @param WebDriverElement $element Expected value of searching type.
	 *
	 * @return WebDriverElement[]|WebDriverLocatable[]|RemoteWebElement[] Expected web driver element.
	 */
	public function arrayByXpath($xPath, WebDriverElement $element = null)
	{
		$by = lcfirst(substr(explode('::', __METHOD__)[1], 7));

		return ($element) ? $this->_arrayBy($by, $xPath, $element) : $this->_arrayBy($by,
		                                                                             $xPath,
		                                                                             $this->webDriver);
	}


	/**
	 * Sets the failed property to true.
	 */
	public function failed()
	{
		$this->testSuite->setFailed(true);
		if($this->failed)
		{
			return $this;
		}
		echo "Element provider deactivated ..\n";
		$this->failed = true;

		return $this;
	}


	/**
	 * True when an error is occurred in the test suite.
	 *
	 * @return bool
	 */
	public function isFailed()
	{
		return $this->failed;
	}


	/**
	 * Returns a web driver element by the given searching type.
	 *
	 * @param string                 $by      Searching type.
	 * @param string                 $value   Expected value of searching type.
	 * @param WebDriverSearchContext $context Instance to find elements.
	 *
	 * @return WebDriverElement Expected web driver element.
	 */
	private function _by($by, $value, WebDriverSearchContext $context)
	{
		if($this->failed)
		{
			return new WebDriverElementNull();
		}
		try
		{
			return $context->findElement(WebDriverBy::$by($value));
		}
		catch(NoSuchElementException $e)
		{
			$this->failed();

			$message =
				$this->testSuite->getSuiteSettings()->getCurrentTestCase()
				. ' | '
				. 'Can not find element by "'
				. $by
				. '" with value "'
				. $value
				. '"';

			$this->fileLogger->log($message, 'errors');
			$this->fileLogger->screenshot($this->webDriver,
			                              $this->testSuite->getSuiteSettings()->getCurrentTestCase()
			                              . '|'
			                              . str_replace(' ', '', $message));

			return new WebDriverElementNull();
		}
	}


	private function _tryBy($by, $value, WebDriverSearchContext $context)
	{
		if($this->failed)
		{
			return new WebDriverElementNull();
		}
		try
		{
			return $context->findElement(WebDriverBy::$by($value));
		}
		catch(NoSuchElementException $e)
		{
			// Todo Not found log message!
			return new WebDriverElementNull();
		}
	}


	/**
	 * Returns an array with web driver elements.
	 *
	 * @param string                 $by      Searching type.
	 * @param string                 $value   Expected value of searching type.
	 * @param WebDriverSearchContext $context Instance to find elements.
	 *
	 * @return WebDriverElement[]|WebDriverLocatable[]|RemoteWebElement[] Array with expected web driver elements.
	 */
	public function _arrayBy($by, $value, WebDriverSearchContext $context)
	{
		return $context->findElements(WebDriverBy::$by($value));
	}
}