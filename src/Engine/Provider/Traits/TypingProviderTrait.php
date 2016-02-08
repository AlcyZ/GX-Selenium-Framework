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

use Facebook\WebDriver\WebDriverElement;
use GXSelenium\Engine\Provider\ElementProvider;

/**
 * Trait TypingProviderTrait
 * @package GXSelenium\Engine\Provider
 */
trait TypingProviderTrait
{
	/**
	 * Types on an web driver element.
	 *
	 * @param WebDriverElement $element Element to interact with.
	 * @param string           $value   Value that should be typed on the element.
	 * @param bool             $clear   Clear the element before typing when true.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function type(WebDriverElement $element, $value, $clear)
	{
		if($this->isFailed())
		{
			return $this;
		}
		($clear) ? $element->clear() : null;
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
	 * Types values on an element by the given name attribute.
	 *
	 * @param string $name  Name attribute of expected element.
	 * @param string $value Value that should be typed on the element.
	 * @param bool   $clear Clear the element before typing when true.
	 *
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
	 * Types values on an element by the given class name.
	 *
	 * @param string $className Class name of expected element.
	 * @param string $value     Value that should be typed on the element.
	 * @param bool   $clear     Clear the element before typing when true.
	 *
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
	 * Types values on an element by the given link text.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param string $value    Value that should be typed on the element.
	 * @param bool   $clear    Clear the element before typing when true.
	 *
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
	 * Types values on an element by the given partial link text.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param string $value           Value that should be typed on the element.
	 * @param bool   $clear           Clear the element before typing when true.
	 *
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
	 * Types values on an element by the given tag name.
	 *
	 * @param string $tagName Tag name of expected element.
	 * @param string $value   Value that should be typed on the element.
	 * @param bool   $clear   Clear the element before typing when true.
	 *
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
	 * Types values on an element by the given css selector.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param string $value       Value that should be typed on the element.
	 * @param bool   $clear       Clear the element before typing when true.
	 *
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
	 * Types values on an element by the given xpath.
	 *
	 * @param string $xpath Xpath of expected element.
	 * @param string $value Value that should be typed on the element.
	 * @param bool   $clear Clear the element before typing when true.
	 *
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
	 * Logs information about the input element.
	 *
	 * @param WebDriverElement $element Expected element to type on.
	 * @param string           $txt     Typed value.
	 *
	 * @return WebDriverElement Passed argument is returned.
	 */
	private function _logTyping(WebDriverElement $element, $txt)
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

		$tag = '<input' . $idValue . $classValue . $nameValue . $typeValue . $placeholderValue . $valueValue . '/>';

		echo "Type\t|\t" . $tag . "\t= " . $txt . "\n";

		return $element;
	}


	/**
	 * Returns the element provider which is required for the trait methods.
	 *
	 * @return ElementProvider
	 */
	abstract public function getElementProvider();


	/**
	 * Returns true when the test case is failed.
	 *
	 * @return bool
	 */
	abstract public function isFailed();
}