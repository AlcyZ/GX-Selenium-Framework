<?php

namespace GXSelenium\Engine\Provider\Traits;

use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use GXSelenium\Engine\Emulator\Client;
use GXSelenium\Engine\NullObjects\WebDriverElementNull;

/**
 * Class ElementProviderTrait
 * @package GXSelenium\Engine\Provider\Traits
 */
trait ElementProviderTrait
{
	/**
	 * Returns a remove web element if found, otherwise a null object with a compatible interface.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods).
	 * @param int         $attempts Attempts until the method will fail.
	 *
	 * @return WebDriverElement
	 */
	public function getBy(WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;

		$element = $this->tryGetBy($by, $attempts);
		if($element instanceof WebDriverElementNull):
			$this->error('Failed to get element by ' . $by->getMechanism() . ' "' . $by->getValue() . '"');
		endif;

		return $element;
	}


	/**
	 * Returns a remove web element by the given id.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $id       Id of expected element.
	 * @param int    $attempts Attempts until the method will fail.
	 *
	 * @return WebDriverElement
	 */
	public function getById($id, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::id($id);

		return $this->getBy($by, $attempts);
	}


	/**
	 * Returns a remove web element by the given name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $name     Name of expected element.
	 * @param int    $attempts Attempts until the method will fail.
	 *
	 * @return WebDriverElement
	 */
	public function getByName($name, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::name($name);

		return $this->getBy($by, $attempts);
	}


	/**
	 * Returns a remove web element by the given class name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $className Class name of expected element.
	 * @param int    $attempts  Attempts until the method will fail.
	 *
	 * @return WebDriverElement
	 */
	public function getByClassName($className, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::className($className);

		return $this->getBy($by, $attempts);
	}


	/**
	 * Returns a remove web element by the given css selector.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $attempts    Attempts until the method will fail.
	 *
	 * @return WebDriverElement
	 */
	public function getByCssSelector($cssSelector, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->getBy($by, $attempts);
	}


	/**
	 * Returns a remove web element by the given link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param int    $attempts Attempts until the method will fail.
	 *
	 * @return WebDriverElement
	 */
	public function getByLinkText($linkText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::linkText($linkText);

		return $this->getBy($by, $attempts);
	}


	/**
	 * Returns a remove web element by the given id.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $attempts        Attempts until the method will fail.
	 *
	 * @return WebDriverElement
	 */
	public function getByPartialLinkText($partialLinkText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->getBy($by, $attempts);
	}


	/**
	 * Returns a remove web element by the given tag name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param int    $attempts Attempts until the method will fail.
	 *
	 * @return WebDriverElement
	 */
	public function getByTagName($tagName, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::tagName($tagName);

		return $this->getBy($by, $attempts);
	}


	/**
	 * Returns a remove web element by the given xpath.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $xpath    Xpath of expected element.
	 * @param int    $attempts Attempts until the method will fail.
	 *
	 * @return WebDriverElement
	 */
	public function getByXpath($xpath, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::xpath($xpath);

		return $this->getBy($by, $attempts);
	}


	/**
	 * Try to returns the expected remove web element.
	 * Returns an null object if no element was found.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods).
	 * @param int         $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement
	 */
	public function tryGetBy(WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$attempt = 0;
		while($attempt < $attempts):
			try
			{
				return $this->getWebDriver()->findElement($by);
			}
			catch(StaleElementReferenceException $e)
			{
				$msg = ($attempt + 1) . '. attempt to get an element by ' . $by->getMechanism() . '"' . $by->getValue()
				       . '" failed, StaleElementReferenceException thrown';
				$this->output($msg);
			}
			catch(\Exception $e)
			{
				$msg = ($attempt + 1) . '. attempt to get an element by ' . $by->getMechanism() . '"' . $by->getValue()
				       . '" failed';
				$ex  = get_class($e) . ' thrown and caught' . "\n";
				$this->output($msg . $ex);
			}
			$attempt++;
		endwhile;

		return $this->_createWebDriverNull();
	}
	

	/**
	 * Try to return a remove web element by the given id.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $id       Id of expected element.
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement
	 */
	public function tryGetById($id, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::id($id);

		return $this->tryGetBy($by, $attempts);
	}


	/**
	 * Try to return a remove web element by the given name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $name     Name of expected element.
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement
	 */
	public function tryGetByName($name, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::name($name);

		return $this->tryGetBy($by, $attempts);
	}


	/**
	 * Try to return a remove web element by the given class name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $className Class name of expected element.
	 * @param int    $attempts  Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement
	 */
	public function tryGetByClassName($className, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::className($className);

		return $this->tryGetBy($by, $attempts);
	}


	/**
	 * Try to return a remove web element by the given css selector.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement
	 */
	public function tryGetByCssSelector($cssSelector, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->tryGetBy($by, $attempts);
	}


	/**
	 * Try to return a remove web element by the given link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement
	 */
	public function tryGetByLinkText($linkText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::linkText($linkText);

		return $this->tryGetBy($by, $attempts);
	}


	/**
	 * Try to return a remove web element by the given partial link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $attempts        Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement
	 */
	public function tryGetByPartialLinkText($partialLinkText, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->tryGetBy($by, $attempts);
	}


	/**
	 * Try to return a remove web element by the given tag name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement
	 */
	public function tryGetByTagName($tagName, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::tagName($tagName);

		return $this->tryGetBy($by, $attempts);
	}


	/**
	 * Try to return a remove web element by the given xpath.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $xpath    Xpath of expected element.
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement
	 */
	public function tryGetByXpath($xpath, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$by = WebDriverBy::xpath($xpath);

		return $this->tryGetBy($by, $attempts);
	}


	/**
	 * Try to get an elements array.
	 * If nothing is found. an empty array will be returned.
	 *
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods).
	 * @param int         $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement[]|array
	 */
	public function tryGetArrayBy(WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return $this->_createWebDriverNull();
		endif;
		$attempt = 0;
		while($attempt < $attempts):
			try
			{
				return $this->getWebDriver()->findElements($by);
			}
			catch(StaleElementReferenceException $e)
			{
				$msg = ($attempt + 1) . '. attempt to get an element by ' . $by->getMechanism() . '"' . $by->getValue()
				       . '" failed, StaleElementReferenceException thrown';
				$this->output($msg);
			}
			catch(\Exception $e)
			{
				$msg = ($attempt + 1) . '. attempt to get an element by ' . $by->getMechanism() . '"' . $by->getValue()
				       . '" failed';
				$ex  = get_class($e) . ' thrown and caught' . "\n";
				$this->output($msg . $ex);
			}
			$attempt++;
		endwhile;

		return [];
	}


	/**
	 * Creates and returns a new web driver element null instance.
	 *
	 * @return WebDriverElementNull
	 */
	private function _createWebDriverNull()
	{
		return new WebDriverElementNull();
	}


	/**
	 * Displays a message on the console.
	 * Delegates to the test case output method.
	 *
	 * @see TestCase::output
	 *
	 * @param string $message Message to display on the console.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	abstract public function output($message);


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


	/**
	 * Logs an error and do a screen shot of the current screen.
	 *
	 * @param string $message Message to log.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	abstract public function error($message);
}