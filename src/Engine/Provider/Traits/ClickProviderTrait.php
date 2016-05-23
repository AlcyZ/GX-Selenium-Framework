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

use Facebook\WebDriver\Remote\RemoteWebElement;
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
	 * Click at the arguments web driver element.
	 *
	 * @param WebDriverElement $element Expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function click(WebDriverElement $element)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$condition = true;
		$counter   = 1;
		while($condition):
			try
			{
				$this->_logClick($element)->click();
				$condition = false;
			}
			catch(\Exception $e)
			{
				echo 'Exception of type "' . get_class($e) . '" thrown. Retry to click, number: ' . $counter . "\n";
				$counter++;

				if($counter > 20):
					$condition = false;
				endif;
			}
		endwhile;

		return $this;
	}


	/**
	 * Click at an element by the given id.
	 *
	 * @param string $id Id of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickId($id)
	{
		$this->click($this->getElementProvider()->byId($id));

		return $this;
	}


	/**
	 * Try to click at an element by the given id.
	 *
	 * @param string $id Id of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickId($id)
	{
		$this->click($this->getElementProvider()->tryById($id));

		return $this;
	}


	/**
	 * Click at an element by the given name attribute.
	 *
	 * @param string $name Name attribute of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickName($name)
	{
		$this->click($this->getElementProvider()->byName($name));

		return $this;
	}


	/**
	 * Try to click at an element by the given name attribute.
	 *
	 * @param string $name Name attribute of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickName($name)
	{
		$this->click($this->getElementProvider()->tryByName($name));

		return $this;
	}


	/**
	 * Click at an element by the given class name.
	 *
	 * @param string $className Class name of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickClassName($className)
	{
		$this->click($this->getElementProvider()->byClassName($className));

		return $this;
	}


	/**
	 * Try to click at an element by the given class name.
	 *
	 * @param string $className Class name of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickClassName($className)
	{
		$this->click($this->getElementProvider()->tryByClassName($className));

		return $this;
	}


	/**
	 * Click at an element by the given link text.
	 *
	 * @param string $linkText Link text of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickLinkText($linkText)
	{
		$this->click($this->getElementProvider()->byLinkText($linkText));

		return $this;
	}


	/**
	 * Try to click at an element by the given link text.
	 *
	 * @param string $linkText Link text of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickLinkText($linkText)
	{
		$this->click($this->getElementProvider()->tryByLinkText($linkText));

		return $this;
	}


	/**
	 * Click at an element by the given partial link text.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickPartialLinkText($partialLinkText)
	{
		$this->click($this->getElementProvider()->byPartialLinkText($partialLinkText));

		return $this;
	}


	/**
	 * Try to click at an element by the given partial link text.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickPartialLinkText($partialLinkText)
	{
		$this->click($this->getElementProvider()->tryByPartialLinkText($partialLinkText));

		return $this;
	}


	/**
	 * Click at an element by the given tag name.
	 *
	 * @param string $tagName Tag name of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickTagName($tagName)
	{
		$this->click($this->getElementProvider()->byTagName($tagName));

		return $this;
	}


	/**
	 * Try to click at an element by the given tag name.
	 *
	 * @param string $tagName Tag name of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickTagName($tagName)
	{
		$this->click($this->getElementProvider()->tryByTagName($tagName));

		return $this;
	}


	/**
	 * Click at an element by the given css selector.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickByCssSelector($cssSelector)
	{
		$this->click($this->getElementProvider()->byCssSelector($cssSelector));

		return $this;
	}


	/**
	 * Try to click at an element by the given css selector.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickByCssSelector($cssSelector)
	{
		$this->click($this->getElementProvider()->tryByCssSelector($cssSelector));

		return $this;
	}


	/**
	 * Click at an element by the given xpath.
	 *
	 * @param string $xPath Xpath of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function clickByXpath($xPath)
	{
		$this->click($this->getElementProvider()->byXpath($xPath));

		return $this;
	}


	/**
	 * Try to click at an element by the given xpath.
	 *
	 * @param string $xPath Xpath of expected element.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function tryClickByXpath($xPath)
	{
		$this->click($this->getElementProvider()->tryByXpath($xPath));

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
	 * Returns true when the test case is failed.
	 *
	 * @return bool
	 */
	abstract public function isFailed();
}