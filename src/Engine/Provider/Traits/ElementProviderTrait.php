<?php

namespace GXSelenium\Engine\Provider\Traits;

use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use GXSelenium\Engine\Emulator\Client;
use GXSelenium\Engine\NullObjects\WebDriverElementNull;
use GXSelenium\Engine\TestSuite;

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

		return $this->_tryBy($by, $attempts);
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
	 * Returns an elements array.
	 * If nothing is found, an empty array will be returned.
	 *
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods).
	 * @param int         $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement[]|array
	 */
	public function getArrayBy(WebDriverBy $by, $attempts = 2)
	{
		if($this->isFailed()):
			return [];
		endif;

		return $this->_tryBy($by, $attempts, 'array');
	}


	/**
	 * Returns an element by the given name.
	 * If nothing is found, an empty array will be returned.
	 *
	 * @param string $name     Name of expected element.
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement[]|array
	 */
	public function getArrayByName($name, $attempts = 2)
	{
		if($this->isFailed()):
			return [];
		endif;
		$by = WebDriverBy::name($name);

		return $this->getArrayBy($by, $attempts);
	}


	/**
	 * Returns an element by the given class name.
	 * If nothing is found, an empty array will be returned.
	 *
	 * @param string $className Class name of expected element.
	 * @param int    $attempts  Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement[]|array
	 */
	public function getArrayByClassName($className, $attempts = 2)
	{
		if($this->isFailed()):
			return [];
		endif;
		$by = WebDriverBy::className($className);

		return $this->getArrayBy($by, $attempts);
	}


	/**
	 * Returns an element by the given css selector.
	 * If nothing is found, an empty array will be returned.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement[]|array
	 */
	public function getArrayByCssSelector($cssSelector, $attempts = 2)
	{
		if($this->isFailed()):
			return [];
		endif;
		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->getArrayBy($by, $attempts);
	}


	/**
	 * Returns an element by the given link text.
	 * If nothing is found, an empty array will be returned.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement[]|array
	 */
	public function getArrayByLinkText($linkText, $attempts = 2)
	{
		if($this->isFailed()):
			return [];
		endif;
		$by = WebDriverBy::linkText($linkText);

		return $this->getArrayBy($by, $attempts);
	}


	/**
	 * Returns an element by the given partial link text.
	 * If nothing is found, an empty array will be returned.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param int    $attempts        Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement[]|array
	 */
	public function getArrayByPartialLinkText($partialLinkText, $attempts = 2)
	{
		if($this->isFailed()):
			return [];
		endif;
		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->getArrayBy($by, $attempts);
	}


	/**
	 * Returns an element by the given tag name.
	 * If nothing is found, an empty array will be returned.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement[]|array
	 */
	public function getArrayByTagName($tagName, $attempts = 2)
	{
		if($this->isFailed()):
			return [];
		endif;
		$by = WebDriverBy::tagName($tagName);

		return $this->getArrayBy($by, $attempts);
	}


	/**
	 * Returns an element array by the given xpath.
	 * If nothing is found, an empty array will be returned.
	 *
	 * @param string $xpath    Xpath of expected element.
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @return WebDriverElement[]|array
	 */
	public function getArrayByXpath($xpath, $attempts = 2)
	{
		if($this->isFailed()):
			return [];
		endif;
		$by = WebDriverBy::xpath($xpath);

		return $this->getArrayBy($by, $attempts);
	}
	

	/**
	 * Wrapper method to handle all element provider methods with the same logic.
	 *
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods).
	 * @param int         $attempts Attempts until the method will fail and return false.
	 * @param string|null $type     When set to "array", an array with remote web elements is returned.
	 *
	 * @return array|\GXSelenium\Engine\NullObjects\WebDriverElementNull|mixed
	 */
	private function _tryBy(WebDriverBy $by, $attempts = 2, $type = null)
	{
		if($this->isFailed()):
			return ($type) ? [] : $this->_createWebDriverNull();
		endif;
		$attempt = 0;
		while($attempt < $attempts):
			try
			{
				$findMethod = $type === 'array' ? 'findElements' : 'findElement';
				
				return call_user_func([$this->getWebDriver(), $findMethod], $by);
			}
			catch(\Exception $e)
			{
				$msg = get_class($e) . ' thrown and caught while trying to get ' . ($attempt + 1) . '. time element by '
				       . $by->getMechanism() . ' "' . $by->getValue() . '"';
				$this->getTestSuite()->getFileLogger()->log($msg . "\n" . $e->getTraceAsString(), 'exceptions');
			}
			$attempt++;
		endwhile;

		return $type === 'array' ? [] : $this->_createWebDriverNull();
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
	 * @param string      $message    Message to log.
	 * @param string|null $errorImage (Optional) Existing error image name.
	 *
	 * @return $this|\GXSelenium\Engine\Emulator\Client Same instance for chained method calls.
	 */
	abstract public function error($message, $errorImage = null);


	/**
	 * Returns the test suite instance.
	 *
	 * @return TestSuite
	 */
	abstract public function getTestSuite();
}