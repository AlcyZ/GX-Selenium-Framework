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
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverSelect;
use GXSelenium\Engine\Emulator\Client;
use GXSelenium\Engine\Provider\ElementProvider;
use GXSelenium\Engine\TestSuite;

/**
 * Trait SelectEmulator
 * @package GXSelenium\Engine\Provider
 */
trait SelectingProviderTrait
{
	/**
	 * Expects to select an elements option by index.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param WebDriverBy $by       WebDriverBy instance to detect the expected element.
	 * @param int         $index    Index of option to be selected.
	 * @param int         $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectIndexByElement(WebDriverBy $by, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->_expectSelectByElement($by, $index, 'index', $attempts);
	}
	

	/**
	 * Selects an elements option by index and the expected elements.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param WebDriverBy $by       WebDriverBy instance to detect the expected element.
	 * @param int         $index    Index of option to be selected.
	 * @param int         $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectIndexByElement(WebDriverBy $by, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectIndexByElement($by, $index, $attempts);
		if(!$result):
			$this->error('Failed to select elements index "' . $index . '" by ' . $by->getMechanism() . ' "'
			             . $by->getValue() . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by index and the elements id.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $id       Id of expected element.
	 * @param int    $index    Index of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectIndexById($id, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::id($id);

		return $this->expectSelectIndexByElement($by, $index, $attempts);
	}
	

	/**
	 * Selects an elements option by index and the elements id.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $id       Id of expected element.
	 * @param int    $index    Index of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectIndexById($id, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectIndexById($id, $index, $attempts);
		if(!$result):
			$this->error('Failed to select elements index "' . $index . '" by id "' . $id . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by index and the elements name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $name     Name of expected element.
	 * @param int    $index    Index of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectIndexByName($name, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::name($name);

		return $this->expectSelectIndexByElement($by, $index, $attempts);
	}


	/**
	 * Selects an elements option by index and the elements name.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $name     Name of expected element.
	 * @param int    $index    Index of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectIndexByName($name, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectIndexByName($name, $index, $attempts);
		if(!$result):
			$this->error('Failed to select elements index "' . $index . '" by name "' . $name . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by index and the elements class name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $className Class name of expected element.
	 * @param int    $index     Index of option to be selected.
	 * @param int    $attempts  Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectIndexByClassName($className, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::className($className);

		return $this->expectSelectIndexByElement($by, $index, $attempts);
	}


	/**
	 * Selects an elements option by index and the elements class name.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $className Class name of expected element.
	 * @param int    $index     Index of option to be selected.
	 * @param int    $attempts  Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectIndexByClassName($className, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectIndexByClassName($className, $index, $attempts);
		if(!$result):
			$this->error('Failed to select elements index "' . $index . '" by class name "' . $className . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by index and the elements css selector.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $index       Index of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectIndexByCssSelector($cssSelector, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->expectSelectIndexByElement($by, $index, $attempts);
	}


	/**
	 * Selects an elements option by index and the elements css selector.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $index       Index of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectIndexByCssSelector($cssSelector, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectIndexByCssSelector($cssSelector, $index, $attempts);
		if(!$result):
			$this->error('Failed to select elements index "' . $index . '" by css selector "' . $cssSelector . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by index and the elements link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param int    $index    Index of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectIndexByLinkText($linkText, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::linkText($linkText);

		return $this->expectSelectIndexByElement($by, $index, $attempts);
	}


	/**
	 * Selects an elements option by index and the elements link text.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param int    $index    Index of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectIndexByLinkText($linkText, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectIndexByLinkText($linkText, $index, $attempts);
		if(!$result):
			$this->error('Failed to select elements index "' . $index . '" by link text "' . $linkText . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by index and the elements partial link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $index           Index of option to be selected.
	 * @param int    $attempts        Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectIndexByPartialLinkText($partialLinkText, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->expectSelectIndexByElement($by, $index, $attempts);
	}


	/**
	 * Selects an elements option by index and the elements id.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $index           Index of option to be selected.
	 * @param int    $attempts        Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectIndexByPartialLinkText($partialLinkText, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectIndexByPartialLinkText($partialLinkText, $index, $attempts);
		if(!$result):
			$this->error('Failed to select elements index "' . $index . '" by partial link text "' . $partialLinkText
			             . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by index and the elements tag name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param int    $index    Index of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectIndexByTagName($tagName, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::tagName($tagName);

		return $this->expectSelectIndexByElement($by, $index, $attempts);
	}


	/**
	 * Selects an elements option by index and the elements tag name.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param int    $index    Index of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectIndexByTagName($tagName, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectIndexByTagName($tagName, $index, $attempts);
		if(!$result):
			$this->error('Failed to select elements index "' . $index . '" by tag name "' . $tagName . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by index and the elements xpath.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $xpath    Xpath of expected element.
	 * @param int    $index    Index of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectIndexByXpath($xpath, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::xpath($xpath);

		return $this->expectSelectIndexByElement($by, $index, $attempts);
	}


	/**
	 * Selects an elements option by index and the elements xpath.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $xpath    Xpath of expected element.
	 * @param int    $index    Index of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectIndexByXpath($xpath, $index, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectIndexByXpath($xpath, $index, $attempts);
		if(!$result):
			$this->error('Failed to select elements index "' . $index . '" by xpath "' . $xpath . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by value.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param WebDriverBy $by       WebDriverBy instance to detect the expected element.
	 * @param int         $value    Value of option to be selected.
	 * @param int         $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectValueByElement(WebDriverBy $by, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->_expectSelectByElement($by, $value, 'value', $attempts);
	}


	/**
	 * Selects an elements option by value and the expected elements.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param WebDriverBy $by       WebDriverBy instance to detect the expected element.
	 * @param int         $value    Value of option to be selected.
	 * @param int         $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectValueByElement(WebDriverBy $by, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectValueByElement($by, $value, $attempts);
		if(!$result):
			$this->error('Failed to select elements value "' . $value . '" by ' . $by->getMechanism() . ' "'
			             . $by->getValue() . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by value and the elements id.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $id       Id of expected element.
	 * @param int    $value    Value of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements value is selected, false otherwise.
	 */
	public function expectSelectValueById($id, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::id($id);

		return $this->expectSelectValueByElement($by, $value, $attempts);
	}


	/**
	 * Selects an elements option by value and the elements id.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $id       Id of expected element.
	 * @param int    $value    Value of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectValueById($id, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectValueById($id, $value, $attempts);
		if(!$result):
			$this->error('Failed to select elements value "' . $value . '" by id "' . $id . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by value and the elements name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $name     Name of expected element.
	 * @param int    $value    Value of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements value is selected, false otherwise.
	 */
	public function expectSelectValueByName($name, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::name($name);

		return $this->expectSelectValueByElement($by, $value, $attempts);
	}


	/**
	 * Selects an elements option by value and the elements name.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $name     Name of expected element.
	 * @param int    $value    Value of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectValueByName($name, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectValueByName($name, $value, $attempts);
		if(!$result):
			$this->error('Failed to select elements value "' . $value . '" by name "' . $name . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by value and the elements class name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $className Class name of expected element.
	 * @param int    $value     Value of option to be selected.
	 * @param int    $attempts  Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements value is selected, false otherwise.
	 */
	public function expectSelectValueByClassName($className, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::className($className);

		return $this->expectSelectValueByElement($by, $value, $attempts);
	}


	/**
	 * Selects an elements option by value and the elements class name.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $className Class name of expected element.
	 * @param int    $value     Value of option to be selected.
	 * @param int    $attempts  Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectValueByClassName($className, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectValueByClassName($className, $value, $attempts);
		if(!$result):
			$this->error('Failed to select elements value "' . $value . '" by class name "' . $className . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by value and the elements css selector.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $value       Value of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements value is selected, false otherwise.
	 */
	public function expectSelectValueByCssSelector($cssSelector, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->expectSelectValueByElement($by, $value, $attempts);
	}


	/**
	 * Selects an elements option by value and the elements css selector.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $value       Value of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectValueByCssSelector($cssSelector, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectValueByCssSelector($cssSelector, $value, $attempts);
		if(!$result):
			$this->error('Failed to select elements value "' . $value . '" by css selector "' . $cssSelector . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by value and the elements link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param int    $value    Value of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements value is selected, false otherwise.
	 */
	public function expectSelectValueByLinkText($linkText, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::linkText($linkText);

		return $this->expectSelectValueByElement($by, $value, $attempts);
	}


	/**
	 * Selects an elements option by value and the elements link text.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param int    $value    Value of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectValueByLinkText($linkText, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectValueByLinkText($linkText, $value, $attempts);
		if(!$result):
			$this->error('Failed to select elements value "' . $value . '" by link text "' . $linkText . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by value and the elements partial link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $value           Value of option to be selected.
	 * @param int    $attempts        Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements value is selected, false otherwise.
	 */
	public function expectSelectValueByPartialLinkText($partialLinkText, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->expectSelectValueByElement($by, $value, $attempts);
	}


	/**
	 * Selects an elements option by value and the elements id.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $value           Value of option to be selected.
	 * @param int    $attempts        Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectValueByPartialLinkText($partialLinkText, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectValueByPartialLinkText($partialLinkText, $value, $attempts);
		if(!$result):
			$this->error('Failed to select elements value "' . $value . '" by partial link text "' . $partialLinkText
			             . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by value and the elements tag name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param int    $value    Value of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements value is selected, false otherwise.
	 */
	public function expectSelectValueByTagName($tagName, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::tagName($tagName);

		return $this->expectSelectValueByElement($by, $value, $attempts);
	}


	/**
	 * Selects an elements option by value and the elements tag name.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param int    $value    Value of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectValueByTagName($tagName, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectValueByTagName($tagName, $value, $attempts);
		if(!$result):
			$this->error('Failed to select elements value "' . $value . '" by tag name "' . $tagName . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by value and the elements xpath.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $xpath    Xpath of expected element.
	 * @param int    $value    Value of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements value is selected, false otherwise.
	 */
	public function expectSelectValueByXpath($xpath, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::xpath($xpath);

		return $this->expectSelectValueByElement($by, $value, $attempts);
	}


	/**
	 * Selects an elements option by value and the elements xpath.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $xpath    Xpath of expected element.
	 * @param int    $value    Value of option to be selected.
	 * @param int    $attempts Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectValueByXpath($xpath, $value, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectValueByXpath($xpath, $value, $attempts);
		if(!$result):
			$this->error('Failed to select elements value "' . $value . '" by xpath "' . $xpath . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by visible text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param WebDriverBy $by          WebDriverBy instance to detect the expected element.
	 * @param int         $visibleText Visible text of option to be selected.
	 * @param int         $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function expectSelectVisibleTextByElement(WebDriverBy $by, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->_expectSelectByElement($by, $visibleText, 'visibleText', $attempts);
	}


	/**
	 * Selects an elements option by visible text and the expected elements.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param WebDriverBy $by          WebDriverBy instance to detect the expected element.
	 * @param int         $visibleText VisibleText of option to be selected.
	 * @param int         $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectVisibleTextByElement(WebDriverBy $by, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectVisibleTextByElement($by, $visibleText, $attempts);
		if(!$result):
			$this->error('Failed to select elements visible text "' . $visibleText . '" by ' . $by->getMechanism()
			             . ' "' . $by->getValue() . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by visible text and the elements id.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $id          Id of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements visible text is selected, false otherwise.
	 */
	public function expectSelectVisibleTextById($id, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::id($id);

		return $this->expectSelectVisibleTextByElement($by, $visibleText, $attempts);
	}


	/**
	 * Selects an elements option by visible text and the elements id.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $id          Id of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectVisibleTextById($id, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectVisibleTextById($id, $visibleText, $attempts);
		if(!$result):
			$this->error('Failed to select elements visible text "' . $visibleText . '" by id "' . $id . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by visible text and the elements name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $name        Name of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements visible text is selected, false otherwise.
	 */
	public function expectSelectVisibleTextByName($name, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::name($name);

		return $this->expectSelectVisibleTextByElement($by, $visibleText, $attempts);
	}


	/**
	 * Selects an elements option by visible text and the elements name.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $name        Name of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectVisibleTextByName($name, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectVisibleTextByName($name, $visibleText, $attempts);
		if(!$result):
			$this->error('Failed to select elements visible text "' . $visibleText . '" by name "' . $name . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by visible text and the elements class name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $className   Class name of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements visibleText is selected, false otherwise.
	 */
	public function expectSelectVisibleTextByClassName($className, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::className($className);

		return $this->expectSelectVisibleTextByElement($by, $visibleText, $attempts);
	}


	/**
	 * Selects an elements option by visible text and the elements class name.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $className   Class name of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectVisibleTextByClassName($className, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectVisibleTextByClassName($className, $visibleText, $attempts);
		if(!$result):
			$this->error('Failed to select elements visible text "' . $visibleText . '" by class name "' . $className
			             . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by visible text and the elements css selector.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements visible text is selected, false otherwise.
	 */
	public function expectSelectVisibleTextByCssSelector($cssSelector, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->expectSelectVisibleTextByElement($by, $visibleText, $attempts);
	}


	/**
	 * Selects an elements option by visible text and the elements css selector.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectVisibleTextByCssSelector($cssSelector, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectVisibleTextByCssSelector($cssSelector, $visibleText, $attempts);
		if(!$result):
			$this->error('Failed to select elements visible text "' . $visibleText . '" by css selector "'
			             . $cssSelector . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by visible text and the elements link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $linkText    Link text of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements visible text is selected, false otherwise.
	 */
	public function expectSelectVisibleTextByLinkText($linkText, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::linkText($linkText);

		return $this->expectSelectVisibleTextByElement($by, $visibleText, $attempts);
	}


	/**
	 * Selects an elements option by visible text and the elements link text.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $linkText    Link text of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectVisibleTextByLinkText($linkText, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectVisibleTextByLinkText($linkText, $visibleText, $attempts);
		if(!$result):
			$this->error('Failed to select elements visible text "' . $visibleText . '" by link text "' . $linkText
			             . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by visible text and the elements partial link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $visibleText     VisibleText of option to be selected.
	 * @param int    $attempts        Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements visible text is selected, false otherwise.
	 */
	public function expectSelectVisibleTextByPartialLinkText($partialLinkText, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->expectSelectVisibleTextByElement($by, $visibleText, $attempts);
	}


	/**
	 * Selects an elements option by visible text and the elements id.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $visibleText     VisibleText of option to be selected.
	 * @param int    $attempts        Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectVisibleTextByPartialLinkText($partialLinkText, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectVisibleTextByPartialLinkText($partialLinkText, $visibleText, $attempts);
		if(!$result):
			$this->error('Failed to select elements visible text "' . $visibleText . '" by partial link text "'
			             . $partialLinkText . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by visible text and the elements tag name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $tagName     Tag name of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements visible text is selected, false otherwise.
	 */
	public function expectSelectVisibleTextByTagName($tagName, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::tagName($tagName);

		return $this->expectSelectVisibleTextByElement($by, $visibleText, $attempts);
	}


	/**
	 * Selects an elements option by visible text and the elements tag name.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $tagName     Tag name of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectVisibleTextByTagName($tagName, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectVisibleTextByTagName($tagName, $visibleText, $attempts);
		if(!$result):
			$this->error('Failed to select elements visible text "' . $visibleText . '" by tag name "' . $tagName
			             . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by visible text and the elements xpath.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param string $xpath       Xpath of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements visible text is selected, false otherwise.
	 */
	public function expectSelectVisibleTextByXpath($xpath, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::xpath($xpath);

		return $this->expectSelectVisibleTextByElement($by, $visibleText, $attempts);
	}


	/**
	 * Selects an elements option by visible text and the elements xpath.
	 * Handles a test case error if the web driver was unable to select the expected element.
	 *
	 * @param string $xpath       Xpath of expected element.
	 * @param int    $visibleText VisibleText of option to be selected.
	 * @param int    $attempts    Amount of retries until the operation will fail.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function selectVisibleTextByXpath($xpath, $visibleText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$result = $this->expectSelectVisibleTextByXpath($xpath, $visibleText, $attempts);
		if(!$result):
			$this->error('Failed to select elements visible text "' . $visibleText . '" by xpath "' . $xpath . '"');
		endif;

		return $this;
	}


	/**
	 * Expects to select an elements option by index.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a exception is thrown.
	 * Returns false when the web driver was unable to select the expected element.
	 *
	 * @param WebDriverBy $by       WebDriverBy instance to detect the expected element.
	 * @param string      $value    Value of option type to be selected.
	 * @param string      $type     Selection type.
	 * @param int         $attempts Amount of retries until the operation will fail.
	 *
	 * @return bool True if the elements index is selected, false otherwise.
	 */
	public function _expectSelectByElement(WebDriverBy $by, $value, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		if($type !== 'index' && $type !== 'value' && $type !== 'visibleText'):
			throw new \UnexpectedValueException('The type argument have to be "index", "value" or "visibleText", current value is "'
			                                    . $type . '"');
		endif;
		$result       = false;
		$attempt      = 0;
		$selectMethod = 'selectBy' . ucfirst($type);

		while($attempt < $attempts):
			try
			{
				$element = $this->getWebDriver()->findElement($by);
				$select  = $this->_createWebDriverSelect($element);
				call_user_func([$select, $selectMethod], $value);
				$this->output("Select\t|\tElement by " . $by->getMechanism() . ' "' . $by->getValue() . '" with '
				              . $type . ' "' . $value . '"');
				$result = true;
				break;
			}
			catch(\Exception $e)
			{
				$msg = get_class($e) . ' thrown and caught while trying to select ' . ($attempt + 1)
				       . '. time element by ' . $by->getMechanism() . ' "' . $by->getValue() . '"';
				$this->getTestSuite()->getFileLogger()->log($msg . "\n" . $e->getTraceAsString(), 'exceptions');
			}
			$attempt++;
		endwhile;

		return $result;
	}


	############################################### select by index ####################################################
	/**
	 * Select an index by the given element.
	 *
	 * @param WebDriverElement $element Select element.
	 * @param int              $index   Index value.
	 *
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * @deprecated Method will be removed in future. Use "::select- or ::expectSelect" methods instead.
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
	 * Returns the web driver instance.
	 *
	 * @return RemoteWebDriver
	 */
	abstract public function getWebDriver();


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


	/**
	 * Returns the test suite instance.
	 *
	 * @return TestSuite
	 */
	abstract public function getTestSuite();
}