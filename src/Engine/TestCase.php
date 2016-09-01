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

namespace GXSelenium\Engine;

use Facebook\WebDriver\Exception\ElementNotVisibleException;
use Facebook\WebDriver\Exception\InvalidSelectorException;
use Facebook\WebDriver\Exception\WebDriverCurlException;
use GXSelenium\Engine\Emulator\Client;

/**
 * Class TestCase
 * @package GXSelenium\Engine\TestCases
 */
abstract class TestCase
{
	/**
	 * @var TestSuite
	 */
	protected $testSuite;

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var bool
	 */
	protected $failed = false;


	/**
	 * Initialize the test case.
	 *
	 * @param TestSuite $testSuite
	 * @param Client    $client
	 */
	public function __construct(TestSuite $testSuite, Client $client)
	{
		$this->testSuite = $testSuite;
		$this->client    = $client;
	}


	/**
	 * Start the test case.
	 *
	 * @Todo Same handling of exceptions. Merge the handling (Change exception parent)
	 */
	public function run()
	{
		$this->client->reset();
		$this->testSuite->getSqlLogger()->startCase($this);
		try
		{
			$this->output("\nStart of " . $this->getCaseName() . '!');
			$this->_run();
		}
		catch(ElementNotVisibleException $e)
		{
			$this->_handleUnexpectedException($e);
		}
		catch(InvalidSelectorException $ex)
		{
			$this->_handleUnexpectedException($ex);
		}
		catch(\Exception $exe)
		{
			$this->_handleUnexpectedException($exe);
		}
		if(!$this->_isFailed())
		{
			$this->output($this->getCaseName() . ' successful!');
		}
		$this->testSuite->getSqlLogger()->endCase($this);
	}


	/**
	 * Checks if the current test case is failed.
	 * When the client or element provider is failed,
	 * the internal test case failed property is set to true.
	 *
	 * @return bool
	 */
	public function _isFailed()
	{
		$clientFailed          = $this->client->isFailed();
		$elementProviderFailed = $this->client->isElementProviderFailed();

		if($clientFailed && $elementProviderFailed):

			$this->failed = true;

		elseif($clientFailed):

			$this->client->getElementProvider()->failed();
			$this->failed = true;

		elseif($elementProviderFailed):

			$this->client->failed();
			$this->failed = true;

		elseif($this->failed):

			$this->client->getElementProvider()->failed();
			$this->client->failed();

		endif;

		return $this->failed;
	}


	/**
	 * Returns the name of the current case.
	 *
	 * @return string
	 */
	public function getCaseName()
	{
		$classNamespaceArray = explode('\\', get_class($this));

		return $classNamespaceArray[count($classNamespaceArray) - 1];
	}


	/**
	 * Logs an error and do a screenshot of the current screen.
	 *
	 * @param string     $message    Error message to log.
	 * @param array|null $errorImage (Optional) Existing error image name.
	 *
	 * @Todo Remove underscore prefix, adjust all method calls.
	 *
	 * @return $this|TestCase Same instance for chained method calls.
	 */
	public function _error($message, $errorImage = null)
	{
		if($this->_isFailed()):
			return $this;
		endif;

		return $this->_prepareAndLogErrorMessage($message, null, $errorImage);
	}


	/**
	 * Logs a thrown exception and do a screenshot of the current screen.
	 *
	 * @param string      $message    Error message of the current case.
	 * @param \Exception  $e          Thrown exception.
	 * @param string|null $errorImage (Optional) Existing error image name.
	 *
	 * @Todo Remove underscore prefix, adjust all method calls.
	 *
	 * @return $this|TestCase Same instance for chained method calls.
	 */
	public function _exceptionError($message, \Exception $e, $errorImage = null)
	{
		if($this->_isFailed()):
			return $this;
		endif;

		return $this->_prepareAndLogErrorMessage($message, $e, $errorImage);
	}


	/**
	 * Prepares the error text and screen shot name.
	 * Afterwards, the ::logData method is called.
	 *
	 * @param string          $message    Error message.
	 * @param \Exception|null $e          (Optional) Exception if thrown.
	 * @param array|null      $errorImage (Optional) Existing error image name.
	 *
	 * @return TestCase|$this Same instance for chained method calls.
	 */
	private function _prepareAndLogErrorMessage($message, \Exception $e = null, $errorImage = null)
	{
		$this->output($message);
		$screenMessage = implode('', array_map('ucfirst', explode(' ', $message)));
		if($e):

			$exceptionArray = explode('\\', get_class($e));
			$exceptionName  = $exceptionArray[count($exceptionArray) - 1];

			$screenName = $this->getCaseName() . ' | ' . $this->_invokedBy() . ' | ' . $screenMessage . ' | '
			              . $exceptionName;
			$txt        = $this->getCaseName() . ' | ' . $this->_invokedBy() . ' | ' . $message . ' | ' . $exceptionName
			              . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";

		else:

			ob_start();
			debug_print_backtrace();
			$backtrace = ob_get_clean();

			$screenName = $this->getCaseName() . ' | ' . $this->_invokedBy() . ' | ' . $screenMessage;
			$txt        = $this->getCaseName() . ' | ' . $this->_invokedBy() . ' | ' . $message . "\n" . $backtrace;

		endif;

		return $this->_logData($message, $txt, $screenName, $errorImage);
	}


	/**
	 * Logs data in the database and filesystem.
	 * A screenshot of the current screen will be created.
	 *
	 * @param string     $message    Error message.
	 * @param string     $txt        Prepared error text.
	 * @param string     $screenName Name of screen shot.
	 * @param array|null $errorImage (Optional) Existing error image name.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	private function _logData($message, $txt, $screenName, $errorImage = null)
	{
		try
		{
			if($errorImage)
			{
				$screenPath = array_shift($errorImage);
			}
			else
			{
				$screenPath = $this->testSuite->getFileLogger()
				                              ->screenshot($this->testSuite->getWebDriver(),
				                                           str_replace(' ', '', $screenName));
			}
		}
		catch(WebDriverCurlException $e)
		{
			$txt .= 'Failed to take a screen shot, additional error information:' . "\nMessage:\t" . $e->getMessage()
			        . "\nStack Trace:\t" . $e->getTraceAsString() . "\n";
			$screenPath = '';
		}
		$this->testSuite->getFileLogger()->log($txt, 'errors');
		$this->testSuite->getSqlLogger()
		                ->caseError($message, $this->testSuite->getWebDriver()->getCurrentURL(), $screenPath);

		$this->addErrorMessages($screenPath, $txt)->failed = true;
		$this->output('TestCaseFailed! ...');

		return $this;
	}


	/**
	 * Adds error messages after an error is occurred.
	 *
	 * @param string $screenShotUrl Path to the error screen shot.
	 * @param string $errorMessage  Error message, which is passed to the FileLogger::log method.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	protected function addErrorMessages($screenShotUrl, $errorMessage)
	{
		$this->testSuite->addErrorMessage('Branch: ' . $this->testSuite->getSuiteSettings()->getBranch());
		$this->testSuite->addErrorMessage('Build number: ' . $this->testSuite->getSuiteSettings()->getBuildNumber());
		$this->testSuite->addErrorMessage('Suite name: ' . $this->testSuite->getSuiteSettings()->getSuiteName());
		$this->testSuite->addErrorMessage('Case: ' . $this->_getCaseName());
		$this->testSuite->addErrorMessage('Test method: ' . $this->_invokedBy(null, 4));
		$this->testSuite->addErrorMessage('Failure url: ' . $this->testSuite->getWebDriver()->getCurrentURL());
		$this->testSuite->addErrorMessage('');
		$this->testSuite->addErrorMessage("Error Message: \n" . $errorMessage);
		$this->testSuite->addErrorMessage('Error time: ' . date('d.m.Y H:i:s'));
		$this->testSuite->addErrorMessage('Screenshot: ' . $this->testSuite->getSuiteSettings()
		                                                                   ->getScreenShotDirectory()
		                                  . DIRECTORY_SEPARATOR . $screenShotUrl);
		$this->testSuite->addErrorMessage('Logfile: [Functionality not developed]');
		$this->testSuite->addErrorMessage('');

		return $this;
	}


	/**
	 * Returns the name of the current case.
	 *
	 * @Todo: Remove this method in future and refactor all usages! Duplicated with public ::getCaseName method.
	 * @deprecated
	 * @return string
	 */
	protected function _getCaseName()
	{
		$classNamespaceArray = explode('\\', get_class($this));

		return $classNamespaceArray[count($classNamespaceArray) - 1];
	}


	/**
	 * Method to output messages in the running console.
	 *
	 * @param string $message                  Message to display.
	 * @param bool   $camelCaseToHumanReadable Converts camel case message to human readable messages.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function output($message, $camelCaseToHumanReadable = false)
	{
		if($this->testSuite->getSuiteSettings()->isLogDisplayed()):
			if($camelCaseToHumanReadable):
				echo $this->camelToSentence($message) . "\n";

				return $this;
			endif;
			echo $message . "\n";
		endif;
		if($this->testSuite->getSuiteSettings()->isLogStored()):
			$this->testSuite->getFileLogger()->log($message, 'log');
		endif;

		return $this;
	}


	/**
	 * Converts a camel case string to an human readable string. (Whitespaces instead of camel case)
	 *
	 * @param string $camelCaseString Input string in camel case format.
	 *
	 * @return string Converted string without camel cases.
	 */
	protected function camelToSentence($camelCaseString)
	{
		return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1 $2', $camelCaseString));
	}


	/**
	 * Returns the method which call the method of current scope without arguments.
	 * The argument must be a key of the debug backtrace array, otherwise the key is automatic set to 'function'.
	 *
	 * @param string|null $type     (Optional) Second key of multidimensional debug backtrace array.
	 * @param int         $deepness (Optional) Deepness of invocation.
	 *
	 * @return mixed|string
	 */
	private function _invokedBy($type = null, $deepness = 2)
	{
		$return = $type ? : 'function';

		$debugBacktrace = debug_backtrace();
		if(!array_key_exists($deepness, $debugBacktrace)):
			throw new \UnexpectedValueException('no invokation with deepness "' . $deepness . '" found in backtrace');
		elseif(!array_key_exists($return, $debugBacktrace[$deepness])):
			$return = 'function';
		endif;

		return $debugBacktrace[$deepness][$return];
	}


	/**
	 * Handles unexpected exception.
	 *
	 * @param \Exception $e
	 */
	private function _handleUnexpectedException(\Exception $e)
	{
		$exceptionStackTrace = $e->getTrace();
		$class               = $exceptionStackTrace[0]['class'];
		$type                = $exceptionStackTrace[0]['type'];
		$method              = $exceptionStackTrace[0]['function'];
		$line                = $exceptionStackTrace[0]['line'];

		$msg = 'Unexpected exception thrown by ' . $class . $type . $method . ' on line ' . $line;
		$this->_exceptionError($msg, $exe);
	}


	/**
	 * Run the test case.
	 *
	 * @return $this|TestCase Same instance for chained method calls.
	 */
	abstract protected function _run();

	######################################## Test Case helper methods ##################################################
	/**
	 * Generates random alphabetical letters.
	 * The $length argument
	 *
	 *
	 * @param int         $length Determine the length of the returned string.
	 * @param string|null $case   Whether 'lower' or 'upper' to transform the string in lower or uppercase. Random if
	 *                            other value is set.
	 *
	 * @return string
	 */
	protected function _randomAlphabeticLetter($length = 1, $case = null)
	{
		$alphabet = 'abcdefghijklmnopqrstuvwxyz';
		$result   = '';
		for($i = 0; $i < $length; $i++)
		{
			if($case === 'upper')
			{
				$result .= ucfirst($alphabet[mt_rand(0, 25)]);
			}
			elseif($case === 'lower')
			{
				$result .= lcfirst($alphabet[mt_rand(0, 25)]);
			}
			else
			{
				$result .= (mt_rand(0, 1) === 0) ? lcfirst($alphabet[mt_rand(0, 25)]) : ucfirst($alphabet[mt_rand(0,
				                                                                                                  25)]);
			}
		}

		return $result;
	}
}