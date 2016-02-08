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

namespace GXSelenium\Engine\NullObjects;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverPoint;

class WebDriverElementNull implements WebDriverElement
{
	/**
	 * If this element is a TEXTAREA or text INPUT element, this will clear the
	 * value.
	 *
	 * @return WebDriverElement The current instance.
	 */
	public function clear()
	{
		// TODO: Implement clear() method.
	}


	/**
	 * Click this element.
	 *
	 * @return WebDriverElement The current instance.
	 */
	public function click()
	{
		// TODO: Implement click() method.
	}


	/**
	 * Get the value of a the given attribute of the element.
	 *
	 * @param string $attribute_name The name of the attribute.
	 *
	 * @return string The value of the attribute.
	 */
	public function getAttribute($attribute_name)
	{
		// TODO: Implement getAttribute() method.
	}


	/**
	 * Get the value of a given CSS property.
	 *
	 * @param string $css_property_name The name of the CSS property.
	 *
	 * @return string The value of the CSS property.
	 */
	public function getCSSValue($css_property_name)
	{
		// TODO: Implement getCSSValue() method.
	}


	/**
	 * Get the location of element relative to the top-left corner of the page.
	 *
	 * @return WebDriverPoint The location of the element.
	 */
	public function getLocation()
	{
		// TODO: Implement getLocation() method.
	}


	/**
	 * Try scrolling the element into the view port and return the location of
	 * element relative to the top-left corner of the page afterwards.
	 *
	 * @return WebDriverPoint The location of the element.
	 */
	public function getLocationOnScreenOnceScrolledIntoView()
	{
		// TODO: Implement getLocationOnScreenOnceScrolledIntoView() method.
	}


	/**
	 * Get the size of element.
	 *
	 * @return WebDriverDimension The dimension of the element.
	 */
	public function getSize()
	{
		// TODO: Implement getSize() method.
	}


	/**
	 * Get the tag name of this element.
	 *
	 * @return string The tag name.
	 */
	public function getTagName()
	{
		// TODO: Implement getTagName() method.
	}


	/**
	 * Get the visible (i.e. not hidden by CSS) innerText of this element,
	 * including sub-elements, without any leading or trailing whitespace.
	 *
	 * @return string The visible innerText of this element.
	 */
	public function getText()
	{
		// TODO: Implement getText() method.
	}


	/**
	 * Is this element displayed or not? This method avoids the problem of having
	 * to parse an element's "style" attribute.
	 *
	 * @return bool
	 */
	public function isDisplayed()
	{
		// Todo Maybe is an error handling required.
		return false;
	}


	/**
	 * Is the element currently enabled or not? This will generally return true
	 * for everything but disabled input elements.
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		// Todo Maybe is an error handling required.
		return false;
	}


	/**
	 * Determine whether or not this element is selected or not.
	 *
	 * @return bool
	 */
	public function isSelected()
	{
		// Todo Maybe is an error handling required.
		return false;
	}


	/**
	 * Simulate typing into an element, which may set its value.
	 *
	 * @param mixed $value The data to be typed.
	 *
	 * @return WebDriverElement The current instance.
	 */
	public function sendKeys($value)
	{
		// Todo Maybe is an error handling required.
		return $this;
	}


	/**
	 * If this current element is a form, or an element within a form, then this
	 * will be submitted to the remote server.
	 *
	 * @return WebDriverElement The current instance.
	 */
	public function submit()
	{
		// Todo Maybe is an error handling required.
		return $this;
	}


	/**
	 * Get the opaque ID of the element.
	 *
	 * @return string The opaque ID.
	 */
	public function getID()
	{
		return null;
		// TODO: Implement getID() method.
	}


	/**
	 * Find the first WebDriverElement within this element using the given
	 * mechanism.
	 *
	 * @param WebDriverBy $locator
	 *
	 * @return WebDriverElement NoSuchElementException is thrown in
	 *    HttpCommandExecutor if no element is found.
	 * @see WebDriverBy
	 */
	public function findElement(WebDriverBy $locator)
	{
		return new WebDriverElementNull();
	}


	/**
	 * Find all WebDriverElements within this element using the given mechanism.
	 *
	 * @param WebDriverBy $locator
	 *
	 * @return WebDriverElement[] A list of all WebDriverElements, or an empty array if
	 *    nothing matches
	 * @see WebDriverBy
	 */
	public function findElements(WebDriverBy $locator)
	{
		// TODO: Implement findElements() method.
	}
}