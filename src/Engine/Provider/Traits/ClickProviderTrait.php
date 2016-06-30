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
	 * @param WebDriverBy $by       WebDriverBy instance to detect the expected element.
	 * @param int         $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectClick(WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$result  = false;
		$attempt = 0;

		while($attempt < $attempts):
			try
			{
				$element = $this->getWebDriver()->findElement($by);
				$this->scrollToElement($element);
				$this->_logClick($element)->click();
				$result = true;
				break;
			}
			catch(StaleElementReferenceException $e)
			{
				if(!empty($element)):
					$text = ($attempt + 1) . '. attempt to click on element '
					        . $this->_getClickingElementsHtml($element) . ' failed';
				else:
					$text = ($attempt + 1) . '. attempt to click on an element which is not found failed';
				endif;
				$text .= "\n";
				$ex = get_class($e) . ' thrown and caught' . "\n";
				echo $text . $ex;
			}
				// Todo: specify exception with more data.
			catch(\Exception $e)
			{
				if(!empty($element)):
					$text = ($attempt + 1) . '. attempt to click on element '
					        . $this->_getClickingElementsHtml($element) . ' failed';
				else:
					$text = ($attempt + 1) . '. attempt to click on an element which is not found failed';
				endif;
				$text .= "\n";
				$ex = get_class($e) . ' thrown and caught' . "\n";
				echo $text . $ex;
			}
			$attempt++;
		endwhile;

		return $result;
	}
	

	/**
	 * Expects that the client clicks an expected element.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a stale element reference exception is thrown.
	 * Returns false when the client was unable to click the element.
	 *
	 * @param WebDriverBy $parentBy WebDriverBy instance to detect the expected parent element.
	 * @param WebDriverBy $by       WebDriverBy instance to detect the expected element.
	 * @param int         $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectClickInside(WebDriverBy $parentBy, WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$result  = false;
		$attempt = 0;

		while($attempt < $attempts):
			try
			{
				$parent  = $this->getWebDriver()->findElement($parentBy);
				$element = $parent->findElement($by);
				$this->scrollToElement($element);
				$this->_logClick($element)->click();
				$result = true;
				break;
			}
			catch(StaleElementReferenceException $e)
			{
				if(!empty($element)):
					$text = ($attempt + 1) . '. attempt to click on element ' . $this->_getElementsHtml($element)
					        . ' failed';
				else:
					$text = ($attempt + 1) . '. attempt to click on an element which is not found failed';
				endif;
				$text .= "\n";
				$ex = get_class($e) . ' thrown and caught' . "\n";
				echo $text . $ex;
			}
				// Todo: specify exception with more data.
			catch(\Exception $e)
			{
				if(!empty($element)):
					$text = ($attempt + 1) . '. attempt to click on element ' . $this->_getElementsHtml($element)
					        . ' failed';
				else:
					$text = ($attempt + 1) . '. attempt to click on an element which is not found failed';
				endif;
				$text .= "\n";
				$ex = get_class($e) . ' thrown and caught' . "\n";
				echo $text . $ex;
			}
			$attempt++;
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
	 * @deprecated Will be removed in future versions. Use expectClick to improve the stability of your test cases.
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
	 * Expects that the client clicks on an element by the given id.
	 *
	 * @param string $id       Id of expected element.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True when click was successful, false otherwise.
	 */
	public function expectClickId($id, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::id($id);

		return $this->expectClick($by, $attempts);
	}


	/**
	 * Click at an element by the given id.
	 *
	 * @param string $id       Id of expected element.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickId($id, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectClickId($id, $attempts);
		if(!$result):
			$this->error('Failed to click on element by id "' . $id . '"');
		endif;

		return $this;
	}


	/**
	 * Try to click at an element by the given id.
	 *
	 * @param string $id              Id of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @deprecated Method will be removed in future versions. Use expectClick[WebDriverBy] to improve the test stability
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickId($id, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryById($id), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Expects that the client clicks on an element by the given name.
	 *
	 * @param string $name     Name of expected element.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True when click was successful, false otherwise.
	 */
	public function expectClickName($name, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		$by = WebDriverBy::name($name);

		return $this->expectClick($by, $attempts);
	}


	/**
	 * Click at an element by the given name attribute.
	 *
	 * @param string $name     Name attribute of expected element.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickName($name, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectClickName($name, $attempts);
		if(!$result):
			$this->error('Failed to click on element by name "' . $name . '"');
		endif;

		return $this;
	}


	/**
	 * Try to click at an element by the given name attribute.
	 *
	 * @param string $name            Name attribute of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @deprecated Method will be removed in future versions. Use expectClick[WebDriverBy] to improve the test stability
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickName($name, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByName($name), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Expects that the client clicks on an element by the given class name.
	 *
	 * @param string $className Class name of expected element.
	 * @param int    $attempts  Amount of retries until the operation will fail.
	 *
	 * @return bool True when click was successful, false otherwise.
	 */
	public function expectClickClassName($className, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		$by = WebDriverBy::className($className);

		return $this->expectClick($by, $attempts);
	}


	/**
	 * Click at an element by the given class name.
	 *
	 * @param string $className Class name of expected element.
	 * @param int    $attempts  Amount of retries until the operation will fail.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickClassName($className, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectClickClassName($className, $attempts);
		if(!$result):
			$this->error('Failed to click on element by class name "' . $className . '"');
		endif;

		return $this;
	}


	/**
	 * Try to click at an element by the given class name.
	 *
	 * @param string $className       Class name of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @deprecated Method will be removed in future versions. Use expectClick[WebDriverBy] to improve the test stability
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickClassName($className, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByClassName($className), $retry, $ignoreException);

		return $this;
	}
	
	
	/**
	 * Expects that the client clicks on an element by the given link text.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True when click was successful, false otherwise.
	 */
	public function expectClickLinkText($linkText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		
		$by = WebDriverBy::linkText($linkText);
		
		return $this->expectClick($by, $attempts);
	}


	/**
	 * Click at an element by the given link text.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickLinkText($linkText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectClickLinkText($linkText, $attempts);
		if(!$result):
			$this->error('Failed to click on element by link text "' . $linkText . '"');
		endif;

		return $this;
	}


	/**
	 * Try to click at an element by the given link text.
	 *
	 * @param string $linkText        Link text of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @deprecated Method will be removed in future versions. Use expectClick[WebDriverBy] to improve the test stability
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickLinkText($linkText, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByLinkText($linkText), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Expects that the client clicks on an element by the given partial link text.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $attempts        Amount of retries until the operation will fail.
	 *
	 * @return bool True when click was successful, false otherwise.
	 */
	public function expectClickPartialLinkText($partialLinkText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->expectClick($by, $attempts);
	}


	/**
	 * Click at an element by the given partial link text.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $attempts        Amount of retries until the operation will fail.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickPartialLinkText($partialLinkText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectClickPartialLinkText($partialLinkText, $attempts);
		if(!$result):
			$this->error('Failed to click on element by partial link text "' . $partialLinkText . '"');
		endif;

		return $this;
	}


	/**
	 * Try to click at an element by the given partial link text.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @deprecated Method will be removed in future versions. Use expectClick[WebDriverBy] to improve the test stability
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickPartialLinkText($partialLinkText, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByPartialLinkText($partialLinkText), $retry, $ignoreException);

		return $this;
	}
	
	
	/**
	 * Expects that the client clicks on an element by the given tag name.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True when click was successful, false otherwise.
	 */
	public function expectClickTagName($tagName, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		
		$by = WebDriverBy::tagName($tagName);
		
		return $this->expectClick($by, $attempts);
	}


	/**
	 * Click at an element by the given tag name.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickTagName($tagName, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectClickTagName($tagName, $attempts);
		if(!$result):
			$this->error('Failed to click on element by attempts "' . $attempts . '"');
		endif;

		return $this;
	}


	/**
	 * Try to click at an element by the given tag name.
	 *
	 * @param string $tagName         Tag name of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @deprecated Method will be removed in future versions. Use expectClick[WebDriverBy] to improve the test stability
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickTagName($tagName, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByTagName($tagName), $retry, $ignoreException);

		return $this;
	}
	
	
	/**
	 * Expects that the client clicks on an element by the given css selector.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True when click was successful, false otherwise.
	 */
	public function expectClickCssSelector($cssSelector, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		
		$by = WebDriverBy::cssSelector($cssSelector);
		
		return $this->expectClick($by, $attempts);
	}


	/**
	 * Click at an element by the given css selector.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickByCssSelector($cssSelector, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectClickCssSelector($cssSelector, $attempts);
		if(!$result):
			$this->error('Failed to click on element by css selector "' . $attempts . '"');
		endif;

		return $this;
	}


	/**
	 * Try to click at an element by the given css selector.
	 *
	 * @param string $cssSelector     Css selector of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @deprecated Method will be removed in future versions. Use expectClick[WebDriverBy] to improve the test stability
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickByCssSelector($cssSelector, $retry = false, $ignoreException = false)
	{
		$this->click($this->getElementProvider()->tryByCssSelector($cssSelector), $retry, $ignoreException);

		return $this;
	}


	/**
	 * Expects that the client clicks on an element by the given xpath.
	 *
	 * @param string $xpath    Xpath of expected element.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True when click was successful, false otherwise.
	 */
	public function expectClickXpath($xpath, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		$by = WebDriverBy::xpath($xpath);

		return $this->expectClick($by, $attempts);
	}


	/**
	 * Click at an element by the given xpath.
	 *
	 * @param string $xPath    Xpath of expected element.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickByXpath($xPath, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectClickXpath($xPath, $attempts);
		if(!$result):
			$this->error('Failed to click on element by xpath "' . $xPath . '"');
		endif;

		return $this;
	}


	/**
	 * Try to click at an element by the given xpath.
	 *
	 * @param string $xPath           Xpath of expected element.
	 * @param bool   $retry           If Set, retry to click element after thrown exception.
	 * @param bool   $ignoreException If true, the case proceed after max amount of thrown exceptions (20).
	 *
	 * @deprecated Method will be removed in future versions. Use expectClick[WebDriverBy] to improve the test stability
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
		$this->output("Click\t|\t" . $this->_getClickingElementsHtml($element));

		return $element;
	}
	

	/**
	 * Returns the html of the expected element.
	 *
	 * @param WebDriverElement $element
	 *
	 * @return string
	 */
	private function _getClickingElementsHtml(WebDriverElement $element)
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

		return '<' . $tagName . $idValue . $classValue . $disabledValue . $hrefValue . '>' . $tagText . '</' . $tagName
		       . '>';
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