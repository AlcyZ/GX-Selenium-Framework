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
use GXSelenium\Engine\Provider\ElementProvider;

/**
 * Class InspectionProvider
 * @package GXSelenium\Engine\Provider
 */
trait InspectionProviderTrait
{
	/**
	 * Client expects that an element is displayed.
	 * Returns true when the element is displayed and false otherwise.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a stale element reference exception is thrown.
	 * Recommended usage in web driver waits.
	 *
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods)
	 * @param int         $attempts (Optional) Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function expectsToBeDisplayed(WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->_expectsToBe('isDisplayed', $by, $attempts);
	}
	
	
	/**
	 * Client expects that an element is displayed.
	 * Returns true when the element is displayed and false otherwise.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a stale element reference exception is thrown.
	 * Recommended usage in web driver waits.
	 *
	 * @param WebDriverBy $parentBy Expected container element (WebDriverBy instance, access via static methods)
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods)
	 * @param int         $attempts (Optional) Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function expectsToBeDisplayedInside(WebDriverBy $parentBy, WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->_expectsToBeInside('isDisplayed', $parentBy, $by, $attempts);
	}

	
	/**
	 * Client expects that an element is selected.
	 * Returns true when the element is selected and false otherwise.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a stale element reference exception is thrown.
	 * Recommended usage in web driver waits.
	 *
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods)
	 * @param int         $attempts (Optional) Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function expectsToBeEnabled(WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->_expectsToBe('isEnabled', $by, $attempts);
	}


	/**
	 * Client expects that an element is enabled.
	 * Returns true when the element is enabled and false otherwise.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a stale element reference exception is thrown.
	 * Recommended usage in web driver waits.
	 *
	 * @param WebDriverBy $parentBy Expected container element (WebDriverBy instance, access via static methods)
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods)
	 * @param int         $attempts (Optional) Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function expectsToBeEnabledInside(WebDriverBy $parentBy, WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->_expectsToBeInside('isEnabled', $parentBy, $by, $attempts);
	}
	

	/**
	 * Client expects that an element is selected.
	 * Returns true when the element is selected and false otherwise.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a stale element reference exception is thrown.
	 * Recommended usage in web driver waits.
	 *
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods)
	 * @param int         $attempts (Optional) Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function expectsToBeSelected(WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->_expectsToBe('isSelected', $by, $attempts);
	}


	/**
	 * Client expects that an element is selected.
	 * Returns true when the element is selected and false otherwise.
	 * Retry the process two times or until the attempts argument count
	 * is reached when a stale element reference exception is thrown.
	 * Recommended usage in web driver waits.
	 *
	 * @param WebDriverBy $parentBy Expected container element (WebDriverBy instance, access via static methods)
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods)
	 * @param int         $attempts (Optional) Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function expectsToBeSelectedInside(WebDriverBy $parentBy, WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;;

		return $this->_expectsToBeInside('isSelected', $parentBy, $by, $attempts);
	}


	/**
	 * Wrapper method for the inspection expectations.
	 * Handles the expectations of displayed, enabled or selected elements.
	 * Recommended usage in web driver waits.
	 *
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods)
	 * @param string      $type     Inspection type, whether isDisplayed, isEnabled or isSelected is allowed.
	 * @param int         $attempts Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	private function _expectsToBe($type, WebDriverBy $by, $attempts)
	{
		if($this->isFailed()):
			return false;
		endif;
		if($type !== 'isDisplayed' && $type !== 'isEnabled' && $type !== 'isSelected'):
			throw new \InvalidArgumentException('the $type argument has to be whether "isDisplayed", "isEnabled" or '
			                                    . '"isSelected"');
		endif;

		$result  = false;
		$attempt = 0;
		while($attempt < $attempts):
			try
			{
				$element = $this->getWebDriver()->findElement($by);
				$result  = call_user_func([$element, $type]);
				break;
			}
			catch(StaleElementReferenceException $e)
			{
				if(!empty($element)):
					$text = ($attempt + 1) . '. attempt to inspect an element '
					        . $this->_getClickingElementsHtml($element) . ' failed';
				else:
					$text = ($attempt + 1) . '. attempt to inspect an element which is not found failed';
				endif;
				$text .= "\n";
				$ex = get_class($e) . ' thrown and caught' . "\n";
				echo $text . $ex;
			}
				// Todo: specify exception with more data.
			catch(\Exception $e)
			{
				if(!empty($element)):
					$text = ($attempt + 1) . '. attempt to inspect an element '
					        . $this->_getClickingElementsHtml($element) . ' failed';
				else:
					$text = ($attempt + 1) . '. attempt to inspect an element which is not found failed';
				endif;
				$text .= "\n";
				$ex = get_class($e) . ' thrown and caught' . "\n";
				echo $text . $ex;
			}
			$attempt++;
		endwhile;

		return $result;
	}
	
	
	private function _expectsToBeInside($type, WebDriverBy $parentBy, WebDriverBy $by, $attempts)
	{
		if($this->isFailed()):
			return false;
		endif;
		if($type !== 'isDisplayed' && $type !== 'isEnabled' && $type !== 'isSelected'):
			throw new \InvalidArgumentException('the $type argument has to be whether "isDisplayed", "isEnabled" or '
			                                    . '"isSelected"');
		endif;

		$result  = false;
		$attempt = 0;
		while($attempt < $attempts):
			try
			{
				$container = $this->getWebDriver()->findElement($parentBy);
				$element   = $container->findElement($by);
				$result    = call_user_func([$element, $type]);
				break;
			}
			catch(StaleElementReferenceException $e)
			{
				if(!empty($element)):
					$text = ($attempt + 1) . '. attempt to inspect an element '
					        . $this->_getClickingElementsHtml($element) . ' failed';
				else:
					$text = ($attempt + 1) . '. attempt to inspect an element which is not found failed';
				endif;
				$text .= "\n";
				$ex = get_class($e) . ' thrown and caught' . "\n";
				echo $text . $ex;
			}
				// Todo: specify exception with more data.
			catch(\Exception $e)
			{
				if(!empty($element)):
					$text = ($attempt + 1) . '. attempt to inspect an element '
					        . $this->_getClickingElementsHtml($element) . ' failed';
				else:
					$text = ($attempt + 1) . '. attempt to inspect an element which is not found failed';
				endif;
				$text .= "\n";
				$ex = get_class($e) . ' thrown and caught' . "\n";
				echo $text . $ex;
			}
			$attempt++;
		endwhile;

		return $result;
	}

	########################################### element is displayed ###################################################
	/**
	 * Check if an element is displayed by the given id.
	 *
	 * @param string                $id      Id of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is displayed, false otherwise.
	 */
	public function isDisplayedById($id, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byId($id, $element)->isDisplayed();
	}


	/**
	 * Check if an element is displayed by the given name attribute.
	 *
	 * @param string                $name    Name attribute of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is displayed, false otherwise.
	 */
	public function isDisplayedByName($name, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byName($name, $element)->isDisplayed();
	}


	/**
	 * Check if an element is displayed by the given class name.
	 *
	 * @param string                $className Class name of expected element.
	 * @param WebDriverElement|null $element   (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is displayed, false otherwise.
	 */
	public function isDisplayedByClassName($className, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byClassName($className, $element)->isDisplayed();
	}


	/**
	 * Check if an element is displayed by the given link text.
	 *
	 * @param string                $linkText Link text of expected element.
	 * @param WebDriverElement|null $element  (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is displayed, false otherwise.
	 */
	public function isDisplayedByLinkText($linkText, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byLinkText($linkText, $element)->isDisplayed();
	}


	/**
	 * Check if an element is displayed by the given partial link text.
	 *
	 * @param string                $partialLinkText Partial link text of expected element.
	 * @param WebDriverElement|null $element         (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is displayed, false otherwise.
	 */
	public function isDisplayedByPartialLinkText($partialLinkText, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byPartialLinkText($partialLinkText, $element)->isDisplayed();
	}


	/**
	 * Check if an element is displayed by the given tag name.
	 *
	 * @param string                $tagName Tag name of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is displayed, false otherwise.
	 */
	public function isDisplayedByTagName($tagName, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byTagName($tagName, $element)->isDisplayed();
	}


	/**
	 * Check if an element is displayed by the given css selector.
	 *
	 * @param string                $cssSelector Css selector of expected element.
	 * @param WebDriverElement|null $element     (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is displayed, false otherwise.
	 */
	public function isDisplayedByCssSelector($cssSelector, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byCssSelector($cssSelector, $element)->isDisplayed();
	}


	/**
	 * Check if an element is displayed by the given xpath.
	 *
	 * @param string                $xpath   Xpath of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is displayed, false otherwise.
	 */
	public function isDisplayedByXpath($xpath, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byXpath($xpath, $element)->isDisplayed();
	}


	########################################### element is selected ####################################################
	/**
	 * Check if an element is selected by the given id.
	 *
	 * @param string                $id      Id of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is selected, false otherwise.
	 */
	public function isSelectedById($id, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byId($id, $element)->isSelected();
	}


	/**
	 * Check if an element is selected by the given name attribute.
	 *
	 * @param string                $name    Name attribute of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is selected, false otherwise.
	 */
	public function isSelectedByName($name, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byName($name, $element)->isSelected();
	}


	/**
	 * Check if an element is selected by the given class name.
	 *
	 * @param string                $className Class name of expected element.
	 * @param WebDriverElement|null $element   (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is selected, false otherwise.
	 */
	public function isSelectedByClassName($className, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byClassName($className, $element)->isSelected();
	}


	/**
	 * Check if an element is selected by the given link text.
	 *
	 * @param string                $linkText Link text of expected element.
	 * @param WebDriverElement|null $element  (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is selected, false otherwise.
	 */
	public function isSelectedByLinkText($linkText, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byLinkText($linkText, $element)->isSelected();
	}


	/**
	 * Check if an element is selected by the given partial link text.
	 *
	 * @param string                $partialLinkText Partial link text of expected element.
	 * @param WebDriverElement|null $element         (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is selected, false otherwise.
	 */
	public function isSelectedByPartialLinkText($partialLinkText, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byPartialLinkText($partialLinkText, $element)->isSelected();
	}


	/**
	 * Check if an element is selected by the given tag name.
	 *
	 * @param string                $tagName Tag name of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is selected, false otherwise.
	 */
	public function isSelectedByTagName($tagName, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byTagName($tagName, $element)->isSelected();
	}


	/**
	 * Check if an element is selected by the given css selector.
	 *
	 * @param string                $cssSelector Css selector of expected element.
	 * @param WebDriverElement|null $element     (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is selected, false otherwise.
	 */
	public function isSelectedByCssSelector($cssSelector, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byCssSelector($cssSelector, $element)->isSelected();
	}


	/**
	 * Check if an element is selected by the given xpath.
	 *
	 * @param string                $xpath   Xpath of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is selected, false otherwise.
	 */
	public function isSelectedByXpath($xpath, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byXpath($xpath, $element)->isSelected();
	}


	############################################ element is enabled ####################################################
	/**
	 * Check if an element is enabled by the given id.
	 *
	 * @param string                $id      Id of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is enabled, false otherwise.
	 */
	public function isEnabledById($id, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byId($id, $element)->isEnabled();
	}


	/**
	 * Check if an element is enabled by the given name attribute.
	 *
	 * @param string                $name    Name attribute of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is enabled, false otherwise.
	 */
	public function isEnabledByName($name, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byName($name, $element)->isEnabled();
	}


	/**
	 * Check if an element is enabled by the given class name.
	 *
	 * @param string                $className Class name of expected element.
	 * @param WebDriverElement|null $element   (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is enabled, false otherwise.
	 */
	public function isEnabledByClassName($className, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byClassName($className, $element)->isEnabled();
	}


	/**
	 * Check if an element is enabled by the given link text.
	 *
	 * @param string                $linkText Link text of expected element.
	 * @param WebDriverElement|null $element  (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is enabled, false otherwise.
	 */
	public function isEnabledByLinkText($linkText, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byLinkText($linkText, $element)->isEnabled();
	}


	/**
	 * Check if an element is enabled by the given partial link text.
	 *
	 * @param string                $partialLinkText Partial link text of expected element.
	 * @param WebDriverElement|null $element         (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is enabled, false otherwise.
	 */
	public function isEnabledByPartialLinkText($partialLinkText, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byPartialLinkText($partialLinkText, $element)->isEnabled();
	}


	/**
	 * Check if an element is enabled by the given tag name.
	 *
	 * @param string                $tagName Tag name of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is enabled, false otherwise.
	 */
	public function isEnabledByTagName($tagName, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byTagName($tagName, $element)->isEnabled();
	}


	/**
	 * Check if an element is enabled by the given css selector.
	 *
	 * @param string                $cssSelector Css selector of expected element.
	 * @param WebDriverElement|null $element     (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is enabled, false otherwise.
	 */
	public function isEnabledByCssSelector($cssSelector, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byCssSelector($cssSelector, $element)->isEnabled();
	}


	/**
	 * Check if an element is enabled by the given xpath.
	 *
	 * @param string                $xpath   Xpath of expected element.
	 * @param WebDriverElement|null $element (Optional) Container element to search in.
	 *
	 * @return bool True if the expected element is enabled, false otherwise.
	 */
	public function isEnabledByXpath($xpath, WebDriverElement $element = null)
	{
		if($this->isFailed())
		{
			return false;
		}

		return $this->getElementProvider()->byXpath($xpath, $element)->isEnabled();
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