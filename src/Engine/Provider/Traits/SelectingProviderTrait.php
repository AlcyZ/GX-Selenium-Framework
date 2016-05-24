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
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverSelect;
use GXSelenium\Engine\Provider\ElementProvider;

/**
 * Trait SelectEmulator
 * @package GXSelenium\Engine\Provider
 */
trait SelectingProviderTrait
{
	############################################### select by index ####################################################
	/**
	 * Select an index by the given element.
	 *
	 * @param WebDriverElement $element Select element.
	 * @param int              $index   Index value.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function indexByElement(WebDriverElement $element, $index)
	{
		return $this->_select($element, 'index', $index);
	}


	/**
	 * Select an index from an element by the given id.
	 *
	 * @param string           $id      Id of expected element.
	 * @param int              $index   Index value.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function indexById($id, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byId($id, $element), 'index', $index);
	}


	/**
	 * Select an index from an element by the given name attribute.
	 *
	 * @param string           $name    Name attribute of expected element.
	 * @param int              $index   Index value.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function indexByName($name, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byName($name, $element), 'index', $index);
	}


	/**
	 * Select an index from an element by the given class name.
	 *
	 * @param string           $className Class name of expected element.
	 * @param int              $index     Index value.
	 * @param WebDriverElement $element   (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function indexByClassName($className, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byClassName($className, $element), 'index', $index);
	}


	/**
	 * Select an index from an element by the given link text.
	 *
	 * @param string           $linkText Link text of expected element.
	 * @param int              $index    Index value.
	 * @param WebDriverElement $element  (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function indexByLinkText($linkText, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byLinkText($linkText, $element), 'index', $index);
	}


	/**
	 * Select an index from an element by the given partial link text.
	 *
	 * @param string           $partialLinkText Partial link text of expected element.
	 * @param int              $index           Index value.
	 * @param WebDriverElement $element         (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function indexByPartialLinkText($partialLinkText, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byPartialLinkText($partialLinkText, $element), 'index',
		                      $index);
	}


	/**
	 * Select an index from an element by the given tag name.
	 *
	 * @param string           $tagName Tag name of expected element.
	 * @param int              $index   Index value.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function indexByTagName($tagName, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byTagName($tagName, $element), 'index', $index);
	}


	/**
	 * Select an index from an element by the given css selector.
	 *
	 * @param string           $cssSelector Css selector of expected element.
	 * @param int              $index       Index value.
	 * @param WebDriverElement $element     (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function indexByCssSelector($cssSelector, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byCssSelector($cssSelector, $element), 'index', $index);
	}


	/**
	 * Select an index from an element by the given xpath.
	 *
	 * @param string           $xpath   Xpath of expected element.
	 * @param int              $index   Index value.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function indexByXpath($xpath, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byXpath($xpath, $element), 'index', $index);
	}


	############################################### select by value ####################################################
	/**
	 * Select a value by the given element.
	 *
	 * @param WebDriverElement $element Select element.
	 * @param int              $value   Value attribute value.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function valueByElement(WebDriverElement $element, $value)
	{
		return $this->_select($element, 'value', $value);
	}


	/**
	 * Select a value from an element by the given id.
	 *
	 * @param string           $id      Id of expected element.
	 * @param int              $index   Value attribute value.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function valueById($id, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byId($id, $element), 'value', $index);
	}


	/**
	 * Select a value from an element by the given name attribute.
	 *
	 * @param string           $name    Name attribute of expected element.
	 * @param int              $index   Value attribute value.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function valueByName($name, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byName($name, $element), 'value', $index);
	}


	/**
	 * Select a value from an element by the given class name.
	 *
	 * @param string           $className Class name of expected element.
	 * @param int              $index     Value attribute value.
	 * @param WebDriverElement $element   (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function valueByClassName($className, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byClassName($className, $element), 'value', $index);
	}


	/**
	 * Select a value from an element by the given link text.
	 *
	 * @param string           $linkText Link text of expected element.
	 * @param int              $index    Value attribute value.
	 * @param WebDriverElement $element  (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function valueByLinkText($linkText, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byLinkText($linkText, $element), 'value', $index);
	}


	/**
	 * Select a value from an element by the given partial link text.
	 *
	 * @param string           $partialLinkText Partial link text of expected element.
	 * @param int              $index           Value attribute value.
	 * @param WebDriverElement $element         (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function valueByPartialLinkText($partialLinkText, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byPartialLinkText($partialLinkText, $element), 'value',
		                      $index);
	}


	/**
	 * Select a value from an element by the given tag name.
	 *
	 * @param string           $tagName Tag name of expected element.
	 * @param int              $index   Value attribute value.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function valueByTagName($tagName, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byTagName($tagName, $element), 'value', $index);
	}


	/**
	 * Select a value from an element by the given css selector.
	 *
	 * @param string           $cssSelector Css selector of expected element.
	 * @param int              $index       Value attribute value.
	 * @param WebDriverElement $element     (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function valueByCssSelector($cssSelector, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byCssSelector($cssSelector, $element), 'value', $index);
	}


	/**
	 * Select a value from an element by the given xpath.
	 *
	 * @param string           $xpath   Xpath of expected element.
	 * @param int              $index   Value attribute value.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function valueByXpath($xpath, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byXpath($xpath, $element), 'value', $index);
	}

	########################################### select by visible text #################################################
	/**
	 * Select a visible text by the given element.
	 *
	 * @param WebDriverElement $element Select element.
	 * @param int              $value   Visible text.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function visibleTextByElement(WebDriverElement $element, $value)
	{
		return $this->_select($element, 'visibleText', $value);
	}


	/**
	 * Select a visible text from an element by the given id.
	 *
	 * @param string           $id      Id of expected element.
	 * @param int              $index   Visible text.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function visibleTextById($id, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byId($id, $element), 'visibleText', $index);
	}


	/**
	 * Select a visible text from an element by the given name attribute.
	 *
	 * @param string           $name    Name attribute of expected element.
	 * @param int              $index   Visible text.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function visibleTextByName($name, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byName($name, $element), 'visibleText', $index);
	}


	/**
	 * Select a visible text from an element by the given class name.
	 *
	 * @param string           $className Class name of expected element.
	 * @param int              $index     Visible text.
	 * @param WebDriverElement $element   (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function visibleTextByClassName($className, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byClassName($className, $element), 'visibleText', $index);
	}


	/**
	 * Select a visible text from an element by the given link text.
	 *
	 * @param string           $linkText Link text of expected element.
	 * @param int              $index    Visible text.
	 * @param WebDriverElement $element  (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function visibleTextByLinkText($linkText, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byLinkText($linkText, $element), 'visibleText', $index);
	}


	/**
	 * Select a visible text from an element by the given partial link text.
	 *
	 * @param string           $partialLinkText Partial link text of expected element.
	 * @param int              $index           Visible text.
	 * @param WebDriverElement $element         (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function visibleTextByPartialLinkText($partialLinkText, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byPartialLinkText($partialLinkText, $element), 'visibleText',
		                      $index);
	}


	/**
	 * Select a visible text from an element by the given tag name.
	 *
	 * @param string           $tagName Tag name of expected element.
	 * @param int              $index   Visible text.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function visibleTextByTagName($tagName, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byTagName($tagName, $element), 'visibleText', $index);
	}


	/**
	 * Select a visible text from an element by the given css selector.
	 *
	 * @param string           $cssSelector Css selector of expected element.
	 * @param int              $index       Visible text.
	 * @param WebDriverElement $element     (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function visibleTextByCssSelector($cssSelector, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byCssSelector($cssSelector, $element), 'visibleText',
		                      $index);
	}


	/**
	 * Select a visible text from an element by the given xpath.
	 *
	 * @param string           $xpath   Xpath of expected element.
	 * @param int              $index   Visible text.
	 * @param WebDriverElement $element (Optional) Container web element.
	 *
	 * @return SelectingProviderTrait|$this Same instance for chained method calls.
	 */
	public function visibleTextByXpath($xpath, $index, WebDriverElement $element = null)
	{
		return $this->_select($this->getElementProvider()->byXpath($xpath, $element), 'visibleText', $index);
	}


	/**
	 * Selects whether an index, value or visible text on an select element.
	 *
	 * @param WebDriverElement $element Expected select element.
	 * @param string           $type    Select whether by index, value or visible text.
	 * @param string|int       $value   Value of whether index, value or visible text.
	 *
	 * @return $this|\GXSelenium\Engine\Provider\Traits\SelectingProviderTrait Same instance for chained method calls.
	 * @throws \Facebook\WebDriver\Exception\StaleElementReferenceException
	 */
	private function _select(WebDriverElement $element, $type, $value)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$driverSelect = $this->_createWebDriverSelect($element);

		$condition = true;
		$counter   = 1;
		while($condition):
			try
			{
				switch($type)
				{
					case 'index':
						$driverSelect->selectByIndex((int)$value);
						break;
					case 'value':
						$driverSelect->selectByValue((string)$value);
						break;
					case 'visibleText':
						$driverSelect->selectByVisibleText((string)$value);
						break;
				}
				$condition = false;
			}
			catch(StaleElementReferenceException $e)
			{
				if($counter >= 50):
					throw $e;
				else:
					echo 'StaleElementReferenceException thrown, retry to select number: ' . $counter . "\n";
					$counter++;
				endif;
			}
		endwhile;

		return $this;
	}


	/**
	 * @param WebDriverElement $element
	 *
	 * @return WebDriverSelect
	 */
	abstract protected function _createWebDriverSelect(WebDriverElement $element);


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