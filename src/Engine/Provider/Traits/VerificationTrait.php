<?php
/* --------------------------------------------------------------
   VerificationTrait.php 06.07.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace GXSelenium\Engine\Provider\Traits;

use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use GXSelenium\Engine\Emulator\Client;
use GXSelenium\Engine\TestSuite;

/**
 * Class VerificationTrait
 * @package GXSelenium\Engine\Provider\Traits
 */
trait VerificationTrait
{
	/**
	 * Verify an input elements value.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param WebDriverBy $by          WebDriverBy instance to detect the expected element.
	 * @param string      $expectation Expected value of value attribute.
	 * @param int         $attempts    Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function verifyInputValueBy(WebDriverBy $by, $expectation, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->verifyBy($by, $expectation, ['attribute' => 'value'], $attempts);
	}
	
	
	/**
	 * Verify an input elements value by id.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $id          Id of expected element.
	 * @param string $expectation Expected value of value attribute.
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function verifyInputValueById($id, $expectation, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::id($id);

		return $this->verifyInputValueBy($by, $expectation, $attempts);
	}


	/**
	 * Verify an input elements value by name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $name        Name of expected element.
	 * @param string $expectation Expected value of value attribute.
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function verifyInputValueByName($name, $expectation, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::name($name);

		return $this->verifyInputValueBy($by, $expectation, $attempts);
	}


	/**
	 * Verify an input elements value by class name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $className   Class name of expected element.
	 * @param string $expectation Expected value of value attribute.
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function verifyInputValueByClassName($className, $expectation, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::className($className);

		return $this->verifyInputValueBy($by, $expectation, $attempts);
	}


	/**
	 * Verify an input elements value by css selector.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param string $expectation Expected value of value attribute.
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function verifyInputValueByCssSelector($cssSelector, $expectation, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->verifyInputValueBy($by, $expectation, $attempts);
	}


	/**
	 * Verify an input elements value by link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $linkText    Link text of expected element.
	 * @param string $expectation Expected value of value attribute.
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function verifyInputValueByLinkText($linkText, $expectation, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::linkText($linkText);

		return $this->verifyInputValueBy($by, $expectation, $attempts);
	}


	/**
	 * Verify an input elements value by partial link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param string $expectation     Expected value of value attribute.
	 * @param int    $attempts        Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function verifyInputValueByPartialLinkText($partialLinkText, $expectation, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->verifyInputValueBy($by, $expectation, $attempts);
	}


	/**
	 * Verify an input elements value by id.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $tagName     Tag name of expected element.
	 * @param string $expectation Expected value of value attribute.
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function verifyInputValueByTagName($tagName, $expectation, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::tagName($tagName);

		return $this->verifyInputValueBy($by, $expectation, $attempts);
	}


	/**
	 * Verify an input elements value by xpath.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $xpath       Xpath of expected element.
	 * @param string $expectation Expected value of value attribute.
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @return bool
	 */
	public function verifyInputValueByXpath($xpath, $expectation, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::xpath($xpath);

		return $this->verifyInputValueBy($by, $expectation, $attempts);
	}


	/**
	 * Verify an element.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param WebDriverBy $by          WebDriverBy instance to detect the expected element.
	 * @param string      $expectation Compare value.
	 * @param string      $type        Verification type (elements text, attributes ...).
	 * @param int         $attempts    Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyBy(WebDriverBy $by, $expectation, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->_verifyBy($by, $expectation, $type, $attempts);
	}
	
	
	/**
	 * Verify an element by id.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $id          Id of expected element.
	 * @param string $expectation Compare value.
	 * @param string $type        Verification type (elements text, attributes ...).
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyById($id, $expectation, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::id($id);
		
		return $this->_verifyBy($by, $expectation, $type, $attempts);
	}
	
	
	/**
	 * Verify an element by name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $name        Name of expected element.
	 * @param string $expectation Compare value.
	 * @param string $type        Verification type (elements text, attributes ...).
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyByName($name, $expectation, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::name($name);
		
		return $this->_verifyBy($by, $expectation, $type, $attempts);
	}
	
	
	/**
	 * Verify an element by class name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $className   Class name of expected element.
	 * @param string $expectation Compare value.
	 * @param string $type        Verification type (elements text, attributes ...).
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyByClassName($className, $expectation, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::className($className);
		
		return $this->_verifyBy($by, $expectation, $type, $attempts);
	}
	
	
	/**
	 * Verify an element by css selector.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param string $expectation Compare value.
	 * @param string $type        Verification type (elements text, attributes ...).
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyByCssSelector($cssSelector, $expectation, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::cssSelector($cssSelector);
		
		return $this->_verifyBy($by, $expectation, $type, $attempts);
	}
	
	
	/**
	 * Verify an element by link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $linkText    Link text of expected element.
	 * @param string $expectation Compare value.
	 * @param string $type        Verification type (elements text, attributes ...).
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyByLinkText($linkText, $expectation, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::linkText($linkText);
		
		return $this->_verifyBy($by, $expectation, $type, $attempts);
	}
	
	
	/**
	 * Verify an element by partial link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param string $expectation     Compare value.
	 * @param string $type            Verification type (elements text, attributes ...).
	 * @param int    $attempts        Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyByPartialLinkText($partialLinkText, $expectation, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::partialLinkText($partialLinkText);
		
		return $this->_verifyBy($by, $expectation, $type, $attempts);
	}
	
	
	/**
	 * Verify an element by tag name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $tagName     Tag name of expected element.
	 * @param string $expectation Compare value.
	 * @param string $type        Verification type (elements text, attributes ...).
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyByTagName($tagName, $expectation, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::tagName($tagName);
		
		return $this->_verifyBy($by, $expectation, $type, $attempts);
	}
	
	
	/**
	 * Verify an element by xpath.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $xpath       Xpath of expected element.
	 * @param string $expectation Compare value.
	 * @param string $type        Verification type (elements text, attributes ...).
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyByXpath($xpath, $expectation, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::xpath($xpath);
		
		return $this->_verifyBy($by, $expectation, $type, $attempts);
	}
	

	/**
	 * Verify an element with a regular expression.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param WebDriverBy $by       Expected element (WebDriverBy instance, access via static methods).
	 * @param string      $regex    Regular expression to compare with.
	 * @param string      $type     Verification type (elements text, attributes ...).
	 * @param int         $attempts Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyRegexBy(WebDriverBy $by, $regex, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;

		return $this->_verifyBy($by, $regex, $type, $attempts, true);
	}
	

	/**
	 * Verify an element with a regular expression by id.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $id       Id of expected element.
	 * @param string $regex    Regular expression to compare with.
	 * @param string $type     Verification type (elements text, attributes ...).
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyRegexById($id, $regex, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::id($id);

		return $this->_verifyBy($by, $regex, $type, $attempts, true);
	}


	/**
	 * Verify an element with a regular expression by name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $name     Name of expected element.
	 * @param string $regex    Regular expression to compare with.
	 * @param string $type     Verification type (elements text, attributes ...).
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyRegexByName($name, $regex, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::name($name);

		return $this->_verifyBy($by, $regex, $type, $attempts, true);
	}


	/**
	 * Verify an element with a regular expression by class name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $className Class name of expected element.
	 * @param string $regex     Regular expression to compare with.
	 * @param string $type      Verification type (elements text, attributes ...).
	 * @param int    $attempts  Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyRegexByClassName($className, $regex, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::className($className);

		return $this->_verifyBy($by, $regex, $type, $attempts, true);
	}


	/**
	 * Verify an element with a regular expression by css selector.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $cssSelector Css selector of expected element.
	 * @param string $regex       Regular expression to compare with.
	 * @param string $type        Verification type (elements text, attributes ...).
	 * @param int    $attempts    Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyRegexByCssSelector($cssSelector, $regex, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::cssSelector($cssSelector);

		return $this->_verifyBy($by, $regex, $type, $attempts, true);
	}


	/**
	 * Verify an element with a regular expression by link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $linkText Link text of expected element.
	 * @param string $regex    Regular expression to compare with.
	 * @param string $type     Verification type (elements text, attributes ...).
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyRegexByLinkText($linkText, $regex, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::linkText($linkText);

		return $this->_verifyBy($by, $regex, $type, $attempts, true);
	}


	/**
	 * Verify an element with a regular expression by partial link text.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $partialLinkText Partial link text of expected element.
	 * @param string $regex           Regular expression to compare with.
	 * @param string $type            Verification type (elements text, attributes ...).
	 * @param int    $attempts        Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyRegexByPartialLinkText($partialLinkText, $regex, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::partialLinkText($partialLinkText);

		return $this->_verifyBy($by, $regex, $type, $attempts, true);
	}


	/**
	 * Verify an element with a regular expression by tag name.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $tagName  Tag name of expected element.
	 * @param string $regex    Regular expression to compare with.
	 * @param string $type     Verification type (elements text, attributes ...).
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyRegexByTagName($tagName, $regex, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::tagName($tagName);

		return $this->_verifyBy($by, $regex, $type, $attempts, true);
	}


	/**
	 * Verify an element with a regular expression by xpath.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param string $xpath    Xpath of expected element.
	 * @param string $regex    Regular expression to compare with.
	 * @param string $type     Verification type (elements text, attributes ...).
	 * @param int    $attempts Attempts until the method will fail and return false.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	public function verifyRegexByXpath($xpath, $regex, $type, $attempts = 2)
	{
		if($this->isFailed()):
			return false;
		endif;
		$by = WebDriverBy::xpath($xpath);

		return $this->_verifyBy($by, $regex, $type, $attempts, true);
	}
	

	/**
	 * Wrapper method to handle verifications.
	 * Retry the process two times or until the attempts argument count
	 * is reached when an exception is thrown.
	 *
	 * @param WebDriverBy $by          Expected element (WebDriverBy instance, access via static methods).
	 * @param string      $expectation Compare value.
	 * @param string      $type        Verification type (elements text, attributes ...).
	 * @param int         $attempts    Attempts until the method will fail and return false.
	 * @param bool        $regex       Compare with a regular expression or an equal operator.
	 *
	 * @see VerificationTrait::_validateTypeArgument
	 * @return bool
	 */
	private function _verifyBy(WebDriverBy $by, $expectation, $type, $attempts = 2, $regex = false)
	{
		if($this->isFailed()):
			return false;
		endif;
		$result  = false;
		$attempt = 0;
		$this->_validateTypeArgument($type);

		while($attempt < $attempts):
			try
			{
				$element = $this->getWebDriver()->findElement($by);
				$result  = $this->_verifyElement($element, $expectation, $type, $regex);
				break;
			}
			catch(\Exception $e)
			{
				$msg = get_class($e) . ' thrown and caught while trying to click ' . ($attempt + 1)
				       . '. time on element by ' . $by->getMechanism() . ' "' . $by->getValue() . '"';
				$this->getTestSuite()->getFileLogger()->log($msg . "\n" . $e->getTraceAsString(), 'exceptions');
			}
			$attempt++;
		endwhile;

		return $result;
	}
	

	/**
	 * Verify the passed argument by the given type and mode.
	 *
	 * @param WebDriverElement $element     Expected verification element.
	 * @param string           $expectation Compare value/regex.
	 * @param string           $type        Verification type (elements text, attributes ...).
	 * @param bool             $regex       Compare with a regular expression or an equal operator.
	 *
	 * @return bool
	 */
	private function _verifyElement(WebDriverElement $element, $expectation, $type, $regex)
	{
		if($type === 'text' || $type === 'id' || $type === 'tagName'):
			$method = 'get' . ucfirst($type);

			return ($regex) ? preg_match($expectation, call_user_func([$element, $method]))
			                  === 1 : (string)call_user_func([
				                                                 $element,
				                                                 $method
			                                                 ]) === (string)$expectation;
		else:
			if(array_key_exists('attribute', $type)):
				$method = 'getAttribute';
				$value  = $type['attribute'];
			else:
				$method = 'getCssValue';
				$value  = $type['cssValue'];
			endif;

			return ($regex) ? preg_match($expectation, call_user_func([$element, $method], $value))
			                  === 1 : (string)call_user_func([
				                                                 $element,
				                                                 $method
			                                                 ], $value) === (string)$expectation;
		endif;
	}
	

	/**
	 * Validates the type argument.
	 * The argument have to be whether a string with the allowed values "text", "id" or "tagName".
	 * If the argument is an array, the key "attribute" or "cssValue" is required.
	 *
	 * @param string $type Verification type to check.
	 *
	 * @return $this|Client Same instance for chained method calls.
	 */
	private function _validateTypeArgument($type)
	{
		if($type !== 'text' && $type !== 'id' && $type !== 'tagName'
		   && (!is_array($type)
		       || is_array($type)
		          && !array_key_exists('attribute', $type))
		   && (!is_array($type) || is_array($type) && !array_key_exists('cssValue', $type))
		):
			throw new \UnexpectedValueException('Invalid $type argument, allowed values: "text", "id", "tagName", or'
			                                    . ' as array: ["attribute" => $value], ["cssValue" => $value]');
		endif;

		return $this;
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
	 * Returns the test suite instance.
	 *
	 * @return TestSuite
	 */
	abstract public function getTestSuite();
}