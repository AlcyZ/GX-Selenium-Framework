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

use Facebook\WebDriver\Exception\InvalidElementStateException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use GXSelenium\Engine\Provider\ElementProvider;

/**
 * Trait TypingProviderTrait
 * @package GXSelenium\Engine\Provider
 */
trait TypingProviderTrait
{
	/**
	 * Expects that the client types on an expected element.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the client was unable to type on the element.
	 *
	 * @param WebDriverBy $by       WebDriverBy instance to detect the expected element.
	 * @param string      $value    Value that should be typed on the element.
	 * @param bool        $clear    Clear the element before typing when true.
	 * @param int         $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectType(WebDriverBy $by, $value, $clear = false, $attempts = 2)
	{
		$result  = false;
		$attempt = 0;

		while($attempt < $attempts):
			try
			{
				$element = $this->getWebDriver()->findElement($by);
				if($clear):
					$element->clear();
				endif;
				$this->_logTyping($element, $value, $clear)->sendKeys($value);
				$result = true;
				break;
			}
				// Todo: specify exception with more data.
			catch(\Exception $e)
			{
				if(!empty($element)):
					$text = ($attempt + 1) . '. attempt to type on element ' . $this->_getElementsHtml($element)
					        . ' failed';
				else:
					$text = ($attempt + 1) . '. attempt to type on an element which is not found failed';
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
	 * Types on an web driver element.
	 *
	 * @param WebDriverElement $element Element to interact with.
	 * @param string           $value   Value that should be typed on the element.
	 * @param bool             $clear   Clear the element before typing when true.
	 *
	 * @deprecated Will be removed in future versions. Use expectType to improve the stability of your test cases.
	 * @return $this Same instance for chained method calls.
	 */
	public function type(WebDriverElement $element, $value, $clear)
	{
		if($this->isFailed())
		{
			return $this;
		}

		if($clear):
			$condition = true;
			$counter   = 0;
			while($condition):
				try
				{
					$element->clear();
					$condition = false;
				}
				catch(InvalidElementStateException $e)
				{
					echo InvalidElementStateException::class . ' thrown.';
					if($counter > 50):
						$condition = false;
					endif;
					$counter++;
				}
				catch(StaleElementReferenceException $e)
				{
					echo StaleElementReferenceException::class . ' thrown.';
					if($counter > 50):
						$condition = false;
					endif;
					$counter++;
				}
			endwhile;
		endif;

		$this->_logTyping($element, $value)->sendKeys($value);

		return $this;
	}


	/**
	 * Types values on an element by the given id.
	 *
	 * @param string $id    Id of expected element.
	 * @param string $value Value that should be typed on the element.
	 * @param bool   $clear Clear the element before typing when true.
	 *
	 * @deprecated Will be removed in future versions. Use expectTypeId to improve the stability of your test cases.
	 * @return $this Same instance for chained method calls.
	 */
	public function typeId($id, $value, $clear = true)
	{
		if($this->isFailed())
		{
			return $this;
		}

		return $this->type($this->getElementProvider()->byId($id), $value, $clear);
	}
	

	/**
	 * Types values on an element by the given id.
	 *
	 * @param string $id       Id of expected element.
	 * @param string $value    Value that should be typed on the element.
	 * @param bool   $clear    Clear the element before typing when true.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectTypeId($id, $value, $clear = true, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$by = WebDriverBy::id($id);

		return $this->expectType($by, $value, $clear, $attempts);
	}


	/**
	 * Types values on an element by the given name attribute.
	 *
	 * @param string $name  Name attribute of expected element.
	 * @param string $value Value that should be typed on the element.
	 * @param bool   $clear Clear the element before typing when true.
	 *
	 * @deprecated Will be removed in future versions. Use expectTypeName to improve the stability of your test cases.
	 * @return $this Same instance for chained method calls.
	 */
	public function typeName($name, $value, $clear = true)
	{
		if($this->isFailed())
		{
			return $this;
		}

		return $this->type($this->getElementProvider()->byName($name), $value, $clear);
	}


	/**
	 * Types values on an element by the given name.
	 *
	 * @param string $name     Name attribute of expected element.
	 * @param string $value    Value that should be typed on the element.
	 * @param bool   $clear    Clear the element before typing when true.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectTypeName($name, $value, $clear = true, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$by = WebDriverBy::name($name);

		return $this->expectType($by, $value, $clear, $attempts);
	}


	/**
	 * Types values on an element by the given class name.
	 *
	 * @param string $className Class name of expected element.
	 * @param string $value     Value that should be typed on the element.
	 * @param bool   $clear     Clear the element before typing when true.
	 *
	 * @deprecated Will be removed in future versions. Use expectTypeClassName to improve the stability of your test
	 *             cases.
	 * @return $this Same instance for chained method calls.
	 */
	public function typeClassName($className, $value, $clear = true)
	{
		if($this->isFailed())
		{
			return $this;
		}

		return $this->type($this->getElementProvider()->byClassName($className), $value, $clear);
	}


	/**
	 * Types values on an element by the given class name.
	 *
	 * @param string $className Class name of expected element.
	 * @param string $value     Value that should be typed on the element.
	 * @param bool   $clear     Clear the element before typing when true.
	 * @param int    $attempts  Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectTypeClassName($className, $value, $clear = true, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$by = WebDriverBy::className($className);

		return $this->expectType($by, $value, $clear, $attempts);
	}


	/**
	 * Types values on an element by the given link text.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param string $value    Value that should be typed on the element.
	 * @param bool   $clear    Clear the element before typing when true.
	 *
	 * @deprecated Will be removed in future versions. Use expectTypeLinkText to improve the stability of your test
	 *             cases.
	 * @return $this Same instance for chained method calls.
	 */
	public function typeLinkText($linkText, $value, $clear = true)
	{
		if($this->isFailed())
		{
			return $this;
		}

		return $this->type($this->getElementProvider()->byLinkText($linkText), $value, $clear);
	}


	/**
	 * Types values on an element by the given link text.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param string $value    Value that should be typed on the element.
	 * @param bool   $clear    Clear the element before typing when true.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectTypeLinkText($linkText, $value, $clear = true, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$by = WebDriverBy::linkText($linkText);

		return $this->expectType($by, $value, $clear, $attempts);
	}


	/**
	 * Types values on an element by the given partial link text.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param string $value           Value that should be typed on the element.
	 * @param bool   $clear           Clear the element before typing when true.
	 *
	 * @deprecated Will be removed in future versions. Use expectTypePartialLinkText to improve the stability of your
	 *             test cases.
	 * @return $this Same instance for chained method calls.
	 */
	public function typePartialLinkText($partialLinkText, $value, $clear = true)
	{
		if($this->isFailed())
		{
			return $this;
		}

		return $this->type($this->getElementProvider()->byPartialLinkText($partialLinkText), $value, $clear);
	}


	/**
	 * Types values on an element by the given partial link text.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param string $value           Value that should be typed on the element.
	 * @param bool   $clear           Clear the element before typing when true.
	 * @param int    $attempts        Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectTypePartialLinkText($partialLinkText, $value, $clear = true, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->expectType($by, $value, $clear, $attempts);
	}


	/**
	 * Types values on an element by the given tag name.
	 *
	 * @param string $tagName Tag name of expected element.
	 * @param string $value   Value that should be typed on the element.
	 * @param bool   $clear   Clear the element before typing when true.
	 *
	 * @deprecated Will be removed in future versions. Use expectTypeTagName to improve the stability of your test
	 *             cases.
	 * @return $this Same instance for chained method calls.
	 */
	public function typeTagName($tagName, $value, $clear = true)
	{
		if($this->isFailed())
		{
			return $this;
		}

		return $this->type($this->getElementProvider()->byTagName($tagName), $value, $clear);
	}


	/**
	 * Types values on an element by the given tag name.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param string $value    Value that should be typed on the element.
	 * @param bool   $clear    Clear the element before typing when true.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectTypeTagName($tagName, $value, $clear = true, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$by = WebDriverBy::tagName($tagName);

		return $this->expectType($by, $value, $clear, $attempts);
	}


	/**
	 * Types values on an element by the given css selector.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param string $value       Value that should be typed on the element.
	 * @param bool   $clear       Clear the element before typing when true.
	 *
	 * @deprecated Will be removed in future versions. Use expectTypeCssSelector to improve the stability of your test
	 *             cases.
	 * @return $this Same instance for chained method calls.
	 */
	public function typeCssSelector($cssSelector, $value, $clear = true)
	{
		if($this->isFailed())
		{
			return $this;
		}

		return $this->type($this->getElementProvider()->byCssSelector($cssSelector), $value, $clear);
	}


	/**
	 * Types values on an element by the given css selector.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param string $value       Value that should be typed on the element.
	 * @param bool   $clear       Clear the element before typing when true.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectTypeCssSelector($cssSelector, $value, $clear = true, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->expectType($by, $value, $clear, $attempts);
	}


	/**
	 * Types values on an element by the given xpath.
	 *
	 * @param string $xpath Xpath of expected element.
	 * @param string $value Value that should be typed on the element.
	 * @param bool   $clear Clear the element before typing when true.
	 *
	 * @deprecated Will be removed in future versions. Use expectTypeXpath to improve the stability of your test cases.
	 * @return $this Same instance for chained method calls.
	 */
	public function typeXpath($xpath, $value, $clear = true)
	{
		if($this->isFailed())
		{
			return $this;
		}

		return $this->type($this->getElementProvider()->byXpath($xpath), $value, $clear);
	}


	/**
	 * Types values on an element by the given xpath.
	 *
	 * @param string $xpath    Xpath of expected element.
	 * @param string $value    Value that should be typed on the element.
	 * @param bool   $clear    Clear the element before typing when true.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool
	 */
	public function expectTypeXpath($xpath, $value, $clear = true, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;
		$by = WebDriverBy::xpath($xpath);

		return $this->expectType($by, $value, $clear, $attempts);
	}


	/**
	 * Logs information about the input element.
	 *
	 * @param WebDriverElement $element Expected element to type on.
	 * @param string           $txt     Typed value.
	 * @param bool             $clear   Clear element before typing.
	 *
	 * @return \Facebook\WebDriver\WebDriverElement Passed argument is returned.
	 */
	private function _logTyping(WebDriverElement $element, $txt, $clear = false)
	{
		$tag = $this->_getElementsHtml($element);

		if($clear):
			echo "Clear\t|\t" . $tag;
		endif;
		echo "Type\t|\t" . $tag . "\t= " . $txt . "\n";

		return $element;
	}
	

	/**
	 * Returns the html of the expected element.
	 *
	 * @param WebDriverElement $element
	 *
	 * @return string
	 */
	private function _getElementsHtml(WebDriverElement $element)
	{
		$id          = ($element->getAttribute('id') !== '') ? $element->getAttribute('id') : null;
		$class       = ($element->getAttribute('class') !== '') ? $element->getAttribute('class') : null;
		$name        = ($element->getAttribute('name') !== '') ? $element->getAttribute('name') : null;
		$type        = ($element->getAttribute('type') !== '') ? $element->getAttribute('type') : null;
		$placeholder = ($element->getAttribute('placeholder') !== '') ? $element->getAttribute('placeholder') : null;
		$value       = ($element->getAttribute('value') !== '') ? $element->getAttribute('value') : null;

		$idValue          = ($id) ? ' id="' . trim($id) . '"' : null;
		$classValue       = ($class) ? ' class="' . trim($class) . '"' : null;
		$nameValue        = ($name) ? ' name="' . trim($name) . '"' : null;
		$typeValue        = ($type) ? ' type="' . trim($type) . '"' : null;
		$placeholderValue = ($placeholder) ? ' placeholder="' . trim($placeholder) . '"' : null;
		$valueValue       = ($value) ? ' value="' . trim($value) . '"' : null;

		return '<input' . $idValue . $classValue . $nameValue . $typeValue . $placeholderValue . $valueValue . '/>';
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