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

namespace GXSelenium\Engine\Provider\Traits;

use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\Exception\UnknownServerException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use GXSelenium\Engine\NullObjects\WebDriverElementNull;
use GXSelenium\Engine\Provider\ElementProvider;

/**
 * Trait ClickProviderTrait
 * @package GXSelenium\Engine\Provider
 */
trait ClickProviderTrait
{
	/**
	 * Expects that the client clicks an expected element.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a stale element reference exception is thrown.
	 * Returns false when the client was unable to click the element.
	 *
	 * @param \Facebook\WebDriver\WebDriverBy $by
	 * @param null                            $attempts
	 *
	 * @return bool
	 */
	public function expectClick(WebDriverBy $by, $attempts = null)
	{
		$result   = false;
		$attempts = $attempts ? : 2;

		while($attempts < 2):
			try
			{
				$element = $this->getWebDriver()->findElement($by);
				$this->_logClick($element)->click();
				$result = true;
				break;
			}
			catch(StaleElementReferenceException $e)
			{
			}
			$attempts++;
		endwhile;

		return $result;
	}


	/**
	 * Click at the arguments web driver element.
	 *
	 * @param WebDriverElement $element         Expected element.
	 * @param bool             $retry           If Set, retry to click element after thrown exception.
	 * @param bool             $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 * @throws \Exception
	 * @throws \Facebook\WebDriver\Exception\UnknownServerException
	 */
	public function click(WebDriverElement $element, $retry = false, $ignoreException = false)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$this->_logClick($element);
		if($retry):
			$counter   = 0;
			$condition = true;
			while($condition):
				try
				{
					$element->click();
					$condition = false;
				}
				catch(UnknownServerException $e)
				{
					if($counter >= 20):
						if(!$ignoreException):
							throw $e;
						endif;
						$condition = false;
					else:
						echo '"UnknownServerException" thrown. Retry to click, number: ' . $counter . "\n";
						$counter++;
					endif;
				}
				catch(\Exception $e)
				{
					if($counter >= 20):
						if(!$ignoreException):
							throw $e;
						endif;
						$condition = false;
					else:
						echo 'Exception of type "' . get_class($e) . '" thrown. Retry to click, number: ' . $counter
						     . "\n";
						$counter++;
					endif;
				}
			endwhile;
		else:
			$element->click();
		endif;

		return $this;
	}


	/**
	 * Click at an element by the given id.
	 *
	 * @param string $id              Id of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickId($id, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->byId($id), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Try to click at an element by the given id.
	 *
	 * @param string $id              Id of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickId($id, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryById($id), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Click at an element by the given name attribute.
	 *
	 * @param string $name            Name attribute of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickName($name, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->byName($name), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Try to click at an element by the given name attribute.
	 *
	 * @param string $name            Name attribute of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickName($name, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByName($name), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Click at an element by the given class name.
	 *
	 * @param string $className       Class name of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickClassName($className, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->byClassName($className), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Try to click at an element by the given class name.
	 *
	 * @param string $className       Class name of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickClassName($className, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByClassName($className), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Click at an element by the given link text.
	 *
	 * @param string $linkText        Link text of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickLinkText($linkText, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->byLinkText($linkText), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Try to click at an element by the given link text.
	 *
	 * @param string $linkText        Link text of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickLinkText($linkText, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByLinkText($linkText), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Click at an element by the given partial link text.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickPartialLinkText($partialLinkText, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->byPartialLinkText($partialLinkText), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Try to click at an element by the given partial link text.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickPartialLinkText($partialLinkText, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByPartialLinkText($partialLinkText), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Click at an element by the given tag name.
	 *
	 * @param string $tagName         Tag name of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickTagName($tagName, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->byTagName($tagName), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Try to click at an element by the given tag name.
	 *
	 * @param string $tagName         Tag name of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickTagName($tagName, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByTagName($tagName), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Click at an element by the given css selector.
	 *
	 * @param string $cssSelector     Css selector of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickByCssSelector($cssSelector, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->byCssSelector($cssSelector), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Try to click at an element by the given css selector.
	 *
	 * @param string $cssSelector     Css selector of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickByCssSelector($cssSelector, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByCssSelector($cssSelector), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Click at an element by the given xpath.
	 *
	 * @param string $xPath           Xpath of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickByXpath($xPath, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->byXpath($xPath), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Try to click at an element by the given xpath.
	 *
	 * @param string $xPath           Xpath of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickByXpath($xPath, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByXpath($xPath), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Log information about a web driver element.
	 *
	 * @param WebDriverElement $element Web element from which the information should be locked.
	 *
	 * @return WebDriverElement Passed argument is returned.
	 */
	private function _logClick(WebDriverElement $element)
	{
		if($element instanceof WebDriverElementNull || $this->isFailed()):
			return $element;
		endif;

		$id       = ($element->getAttribute('id') !== '') ? $element->getAttribute('id') : null;
		$class    = ($element->getAttribute('class') !== '') ? $element->getAttribute('class') : null;
		$disabled = ($element->getAttribute('disabled') !== '') ? $element->getAttribute('disabled') : null;
		$href     = ($element->getAttribute('href') !== '') ? $element->getAttribute('href') : null;

		$idValue       = ($id) ? ' id="' . trim($id) . '"' : null;
		$classValue    = ($class) ? ' class="' . trim($class) . '"' : null;
		$disabledValue = ($disabled) ? ' disabled' : null;
		$hrefValue     = ($href) ? ' href="' . trim($href) . '"' : null;

		$tagName = $element->getTagName();
		$tagText = $element->getText();

		$txt = '<' . $tagName . $idValue . $classValue . $disabledValue . $hrefValue . '>' . $tagText . '</' . $tagName
		       . '>';
		echo "Click\t|\t" . $txt . "\n";

		return $element;
	}


	/**
	 * Returns the element provider which is required for the trait methods.
	 *
	 * @return ElementProvider
	 */
	abstract public function getElementProvider();


	/**
	 * Returns the web driver instance.
	 *
	 * @return RemoteWebDriver
	 */
	abstract public function getWebDriver();


	/**
	 * Returns true when the test case is failed.
	 *
	 * @return bool
	 */
	abstract public function isFailed();
}