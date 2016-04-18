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
use Facebook\WebDriver\WebDriver;
use GXSelenium\Engine\Emulator\Client;
use GXSelenium\Engine\Logger\FileLogger;
use GXSelenium\Engine\Logger\SqlLogger;
use GXSelenium\Engine\Settings\SuiteSettings;

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
			echo "\nStart of " . $this->getCaseName() . "!\n";
			$this->_run();
		}
		catch(ElementNotVisibleException $e)
		{
			$this->_exceptionError('todo: try to get method which throws this exception', $e);
		}
		catch(InvalidSelectorException $ex)
		{
			$this->_exceptionError('todo: try to get method which throws this exception', $ex);
		}
		catch(\Exception $exe)
		{
			$this->_exceptionError('todo: try to get method which throws this exception', $exe);
		}
		if(!$this->_isFailed())
		{
			echo $this->getCaseName() . " successful!\n";
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
		return array_pop(explode('\\', get_class($this)));
	}


	/**
	 * Logs an error and do a screenshot of the current screen.
	 *
	 * @param string $message Error message to log.
	 *
	 * @Todo Remove underscore prefix, adjust all method calls.
	 *
	 * @return $this|TestCase Same instance for chained method calls.
	 */
	public function _error($message)
	{
		if($this->_isFailed()):
			return $this;
		endif;

		return $this->_prepareAndLogErrorMessage($message);
	}


	/**
	 * Logs a thrown exception and do a screenshot of the current screen.
	 *
	 * @param string     $message Error message of the current case.
	 * @param \Exception $e       Thrown exception.
	 *
	 * @Todo Remove underscore prefix, adjust all method calls.
	 *
	 * @return $this|TestCase Same instance for chained method calls.
	 */
	public function _exceptionError($message, \Exception $e)
	{
		if($this->_isFailed()):
			return $this;
		endif;

		return $this->_prepareAndLogErrorMessage($message, $e);
	}


	/**
	 * Prepares the error text and screen shot name.
	 * Afterwards, the ::logData method is called.
	 *
	 * @param string          $message Error message.
	 * @param \Exception|null $e       (Optional) Exception if thrown.
	 *
	 * @return TestCase|$this Same instance for chained method calls.
	 */
	private function _prepareAndLogErrorMessage($message, \Exception $e = null)
	{
		$screenMessage = implode('', array_map('ucfirst', explode(' ', $message)));
		if($e):

			$exceptionName = array_pop(explode('\\', get_class($e)));

			$screenName = $this->_getCaseName() . ' | ' . $this->_invokedBy() . ' | ' . $screenMessage . ' | '
			              . $exceptionName;
			$txt        = $this->_getCaseName() . ' | ' . $this->_invokedBy() . ' | ' . $message . ' | '
			              . $exceptionName . "\n" . $e->getTraceAsString() . "\n";

		else:

			ob_start();
			debug_print_backtrace();
			$backtrace = ob_get_clean();

			$screenName = $this->_getCaseName() . ' | ' . $this->_invokedBy() . ' | ' . $screenMessage;
			$txt        = $this->_getCaseName() . ' | ' . $this->_invokedBy() . ' | ' . $message . "\n" . $backtrace;

		endif;

		return $this->_logData($message, $txt, $screenName);
	}


	/**
	 * Logs data in the database and filesystem.
	 * A screenshot of the current screen will be created.
	 *
	 * @param string $message    Error message.
	 * @param string $txt        Prepared error text.
	 * @param string $screenName Name of screen shot.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	private function _logData($message, $txt, $screenName)
	{
		$screenPath = $this->testSuite->getFileLogger()
		                              ->screenshot($this->testSuite->getWebDriver(), str_replace(' ', '', $screenName));

		$this->testSuite->getFileLogger()->log($txt, 'errors');
		$this->testSuite->getSqlLogger()
		                ->caseError($message, $this->testSuite->getWebDriver()->getCurrentURL(), $screenPath);

		$this->addErrorMessages($screenPath, $txt)->failed = true;
		echo "TestCaseFailed ..\n";

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
	 * Wait the specified amount of time until the case will continue.
	 *
	 * @param string $expectedUrlSnippet Snippet of url to match before continue the case.
	 * @param int    $waitTimeout        (Optional) Amount of seconds to wait before the case fail. Default is 5.
	 * @param int    $delay              (Optional) Delay after the page is loaded. Default is 1.
	 * @param string $return             (Optional) Return value, when empty the client instance is returned.
	 *
	 * @return Client|TestCase|$this Whether the same or the client instance, specified by the 3. argument.
	 */
	protected function waitForPageLoaded($expectedUrlSnippet, $waitTimeout = 5, $delay = 0, $return = 'client')
	{
		try
		{
			$this->client->waitForPageLoaded($expectedUrlSnippet, $waitTimeout);
		}
		catch(\Exception $e)
		{
			$this->_exceptionError('Wait to long for page load', $e);
		}
		($delay > 0) ? sleep($delay) : null;

		return ($return === 'client') ? $this->client : $this;
	}


	/**
	 * Returns the name of the current case.
	 *
	 * @return string
	 */
	protected function _getCaseName()
	{
		return array_pop(explode('\\', get_class($this)));
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
	 * Run the test case.
	 *
	 * @return $this|TestCase Same instance for chained method calls.
	 */
	abstract protected function _run();
}