<?php
/* --------------------------------------------------------------
   WaitProviderTrait.php 30.06.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace GXSelenium\Engine\Provider\Traits;

use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use GXSelenium\Engine\Emulator\Client;

/**
 * Class WaitProviderTrait
 * @package GXSelenium\Engine\Provider\Traits
 *
 * @Todo    Implement methods ::waitUntilNot[Type][WebDriverBy]. (type === selected || type === enabled)
 */
trait WaitProviderTrait
{
	/**
	 * Wait until the callback argument returns true, or the set timeouts are reached.
	 *
	 * @param callable $closure      Callback function to determine the waiting condition.
	 * @param string   $errorMessage Message if the timeouts are reached.
	 * @param int      $timeOut      Timeout to wait.
	 * @param int      $interval     Interval (in milliseconds) in which the callback function is called.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	public function waitUntil(callable $closure, $errorMessage, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		try
		{
			$this->getWebDriver()->wait($timeOut, $interval)->until($closure);
		}
		catch(TimeOutException $e)
		{
			$this->output($errorMessage);
			$this->exceptionError($errorMessage, $e);
		}

		return $this;
	}


	/**
	 * Wait until an element is displayed or the value of the timeout argument is achieved.
	 *
	 * @param WebDriverBy $by       WebDriverBy instance to locate the expected element.
	 * @param int         $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int         $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilElementIsDisplayed(WebDriverBy $by, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		return $this->_waitUntil('displayed', $by, $timeOut, $interval);
	}
	

	/**
	 * Wait until an element is displayed by the given id or the value of the timeout argument is achieved.
	 *
	 * @param int $id       Id of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilIdIsDisplayed($id, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::id($id);

		return $this->waitUntilElementIsDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given class name or the value of the timeout argument is achieved.
	 *
	 * @param int $className ClassName of expected element.
	 * @param int $timeOut   Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval  Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilClassNameIsDisplayed($className, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::className($className);

		return $this->waitUntilElementIsDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given name or the value of the timeout argument is achieved.
	 *
	 * @param int $name     Name of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilNameIsDisplayed($name, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::name($name);

		return $this->waitUntilElementIsDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given css selector or the value of the timeout argument is achieved.
	 *
	 * @param int $cssSelector Css selector of expected element.
	 * @param int $timeOut     Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval    Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilCssSelectorIsDisplayed($cssSelector, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->waitUntilElementIsDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given link text or the value of the timeout argument is achieved.
	 *
	 * @param int $linkText Link text of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilLinkTextIsDisplayed($linkText, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::linkText($linkText);

		return $this->waitUntilElementIsDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given partial link text or the value of the timeout argument is
	 * achieved.
	 *
	 * @param int $partialLinkText Partial link text of expected element.
	 * @param int $timeOut         Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval        Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilPartialLinkTextIsDisplayed($partialLinkText, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->waitUntilElementIsDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given tag name or the value of the timeout argument is achieved.
	 *
	 * @param int $tagName  Tag name of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilTagNameIsDisplayed($tagName, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::tagName($tagName);

		return $this->waitUntilElementIsDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given xpath or the value of the timeout argument is achieved.
	 *
	 * @param int $xPath    Xpath of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilXpathIsDisplayed($xPath, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::xpath($xPath);

		return $this->waitUntilElementIsDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed or the value of the timeout argument is achieved.
	 *
	 * @param WebDriverBy $by       WebDriverBy instance to locate the expected element.
	 * @param int         $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int         $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilElementIsNotDisplayed(WebDriverBy $by, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		return $this->_waitUntil('displayed', $by, $timeOut, $interval, false);
	}


	/**
	 * Wait until an element is displayed by the given id or the value of the timeout argument is achieved.
	 *
	 * @param int $id       Id of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilIdIsNotDisplayed($id, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::id($id);

		return $this->waitUntilElementIsNotDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given class name or the value of the timeout argument is achieved.
	 *
	 * @param int $className ClassName of expected element.
	 * @param int $timeOut   Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval  Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilClassNameIsNotDisplayed($className, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::className($className);

		return $this->waitUntilElementIsNotDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given name or the value of the timeout argument is achieved.
	 *
	 * @param int $name     Name of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilNameIsNotDisplayed($name, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::name($name);

		return $this->waitUntilElementIsNotDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given css selector or the value of the timeout argument is achieved.
	 *
	 * @param int $cssSelector Css selector of expected element.
	 * @param int $timeOut     Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval    Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilCssSelectorIsNotDisplayed($cssSelector, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->waitUntilElementIsNotDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given link text or the value of the timeout argument is achieved.
	 *
	 * @param int $linkText Link text of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilLinkTextIsNotDisplayed($linkText, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::linkText($linkText);

		return $this->waitUntilElementIsNotDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given partial link text or the value of the timeout argument is
	 * achieved.
	 *
	 * @param int $partialLinkText Partial link text of expected element.
	 * @param int $timeOut         Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval        Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilPartialLinkTextIsNotDisplayed($partialLinkText, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->waitUntilElementIsNotDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given tag name or the value of the timeout argument is achieved.
	 *
	 * @param int $tagName  Tag name of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilTagNameIsNotDisplayed($tagName, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::tagName($tagName);

		return $this->waitUntilElementIsNotDisplayed($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is displayed by the given xpath or the value of the timeout argument is achieved.
	 *
	 * @param int $xPath    Xpath of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilXpathIsNotDisplayed($xPath, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::xpath($xPath);

		return $this->waitUntilElementIsNotDisplayed($by, $timeOut, $interval);
	}
	

	/**
	 * Wait until an element is selected or the value of the timeout argument is achieved.
	 *
	 * @param WebDriverBy $by       WebDriverBy instance to locate the expected element.
	 * @param int         $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int         $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilElementIsSelected(WebDriverBy $by, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		return $this->_waitUntil('selected', $by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is selected by the given id or the value of the timeout argument is achieved.
	 *
	 * @param int $id       Id of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilIdIsSelected($id, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::id($id);

		return $this->waitUntilElementIsSelected($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is selected by the given class name or the value of the timeout argument is achieved.
	 *
	 * @param int $className Class name of expected element.
	 * @param int $timeOut   Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval  Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilClassNameIsSelected($className, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::className($className);

		return $this->waitUntilElementIsSelected($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is selected by the given name or the value of the timeout argument is achieved.
	 *
	 * @param int $name     Name of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilNameIsSelected($name, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::name($name);

		return $this->waitUntilElementIsSelected($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is selected by the given css selector or the value of the timeout argument is achieved.
	 *
	 * @param int $cssSelector Css selector of expected element.
	 * @param int $timeOut     Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval    Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilCssSelectorIsSelected($cssSelector, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->waitUntilElementIsSelected($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is selected by the given link text or the value of the timeout argument is achieved.
	 *
	 * @param int $linkText Link text of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilLinkTextIsSelected($linkText, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::linkText($linkText);

		return $this->waitUntilElementIsSelected($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is selected by the given id partial link text the value of the timeout argument is
	 * achieved.
	 *
	 * @param int $partialLinkText Partial link of expected element.
	 * @param int $timeOut         Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval        Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilPartialLinkTextIsSelected($partialLinkText, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->waitUntilElementIsSelected($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is selected by the given tag name or the value of the timeout argument is achieved.
	 *
	 * @param int $tagName  Tag name of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilTagNameIsSelected($tagName, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::tagName($tagName);

		return $this->waitUntilElementIsSelected($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is selected by the given xpath or the value of the timeout argument is achieved.
	 *
	 * @param int $xPath    Xpath of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilXpathIsSelected($xPath, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::xpath($xPath);

		return $this->waitUntilElementIsSelected($by, $timeOut, $interval);
	}
	

	/**
	 * Wait until an element is enabled or the value of the timeout argument is achieved.
	 *
	 * @param WebDriverBy $by       WebDriverBy instance to locate the expected element.
	 * @param int         $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int         $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilElementIsEnabled(WebDriverBy $by, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		return $this->_waitUntil('enabled', $by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is enabled by the given id or the value of the timeout argument is achieved.
	 *
	 * @param int $id       Id of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilIdIsEnabled($id, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::id($id);

		return $this->waitUntilElementIsEnabled($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is enabled by the given class name or the value of the timeout argument is achieved.
	 *
	 * @param int $className Class name of expected element.
	 * @param int $timeOut   Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval  Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilClassNameIsEnabled($className, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::className($className);

		return $this->waitUntilElementIsEnabled($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is enabled by the given name or the value of the timeout argument is achieved.
	 *
	 * @param int $name     Name of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilNameIsEnabled($name, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::name($name);

		return $this->waitUntilElementIsEnabled($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is enabled by the given css selector or the value of the timeout argument is achieved.
	 *
	 * @param int $cssSelector Css selector of expected element.
	 * @param int $timeOut     Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval    Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilCssSelectorIsEnabled($cssSelector, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->waitUntilElementIsEnabled($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is enabled by the given link text or the value of the timeout argument is achieved.
	 *
	 * @param int $linkText Link text of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilLinkTextIsEnabled($linkText, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::linkText($linkText);

		return $this->waitUntilElementIsEnabled($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is enabled by the given partial link text or the value of the timeout argument is achieved.
	 *
	 * @param int $partialLinkText Partial link text of expected element.
	 * @param int $timeOut         Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval        Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilPartialLinkTextIsEnabled($partialLinkText, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->waitUntilElementIsEnabled($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is enabled by the given tag name or the value of the timeout argument is achieved.
	 *
	 * @param int $tagName  Tag name of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilTagNameIsEnabled($tagName, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::tagName($tagName);

		return $this->waitUntilElementIsEnabled($by, $timeOut, $interval);
	}


	/**
	 * Wait until an element is enabled by the given xpath or the value of the timeout argument is achieved.
	 *
	 * @param int $xPath    Xpath of expected element.
	 * @param int $timeOut  Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int $interval Interval of repeating the waiting condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function waitUntilXpathIsEnabled($xPath, $timeOut = 30, $interval = 250)
	{
		if($this->isFailed()):
			return $this;
		endif;

		$by = WebDriverBy::xpath($xPath);

		return $this->waitUntilElementIsEnabled($by, $timeOut, $interval);
	}


	/**
	 * Main method to handle waits. All public accessible methods in this trait delegates to this method
	 * to handle the waiting process with the same algorithm. When the timeout is reached an error gets handled
	 * automatically. After the waiting process, the test case will continue.
	 *
	 * @param string      $type        Whether displayed, enabled or selected.
	 * @param WebDriverBy $by          WebDriverBy instance to locate the expected element.
	 * @param int         $timeOut     Timeout to wait, when the amount (in seconds) is reached, the test case fails.
	 * @param int         $interval    Interval of repeating the waiting condition.
	 * @param bool        $expectation Possibility to negate the wait condition.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	private function _waitUntil($type, WebDriverBy $by, $timeOut = 30, $interval = 250, $expectation = true)
	{
		if($this->isFailed()):
			return $this;
		endif;
		if($type !== 'displayed' && $type !== 'enabled' && $type !== 'selected'):
			throw new \InvalidArgumentException('the $type argument has to be whether "displayed", "enabled" or '
			                                    . '"selected"');
		endif;

		$this->output('Wait ' . $timeOut . ' seconds until an element with ' . $by->getMechanism() . ' "'
		              . $by->getValue() . '" is ' . $type);

		$expectMethod = 'expectsToBe' . ucfirst($type);
		try
		{
			$this->getWebDriver()->wait($timeOut, $interval)->until(function () use ($by, $expectMethod, $expectation)
			{
				return call_user_func([$this, $expectMethod], $by) === $expectation;
			});
		}
		catch(TimeOutException $e)
		{
			$msg = $by->getMechanism() . ' "' . $by->getValue() . '" not found in ' . $timeOut . ' seconds.';
			$this->output($msg);
			$this->exceptionError($msg, $e);
		}

		return $this;
	}


	/**
	 * Returns the web driver instance.
	 *
	 * @return RemoteWebDriver
	 */
	abstract public function getWebDriver();
}