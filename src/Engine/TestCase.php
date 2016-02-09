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
	 * @var WebDriver
	 */
	protected $webDriver;

	/**
	 * @var SuiteSettings
	 */
	protected $suiteSettings;

	/**
	 * @var bool
	 */
	protected $failed = false;

	/**
	 * @var FileLogger
	 */
	protected $fileLogger;

	/**
	 * @var SqlLogger
	 */
	protected $sqlLogger;

	/**
	 * Initialize the test case.
	 *
	 * @param TestSuite $testSuite
	 */
	public function __construct(TestSuite $testSuite, Client $client)
	{
		$this->testSuite     = $testSuite;
		$this->webDriver     = $testSuite->getWebDriver();
		$this->suiteSettings = $testSuite->getSuiteSettings();

		$this->client     = $client;
		$this->fileLogger = $this->testSuite->getFileLogger();
		$this->sqlLogger  = $this->testSuite->getSqlLogger();
	}


	/**
	 * Start the test case.
	 *
	 * @Todo Same handling of exceptions. Merge the handling (Change exception parent)
	 */
	public function run()
	{
		$this->client->reset();
		$this->sqlLogger->startCase($this);
		try
		{
			echo "\nStart of " . $this->getCaseName() . "!\n";
			$this->_run();
		}
		catch(ElementNotVisibleException $e)
		{
			$this->_exceptionError($this->getCaseName(), explode('::', __METHOD__)[1], $e);
		}
		catch(InvalidSelectorException $ex)
		{
			$this->_exceptionError($this->getCaseName(), explode('::', __METHOD__)[1], $ex);
		}
		if(!$this->_isFailed())
		{
			echo $this->getCaseName() . " successful!\n";
		}
		$this->sqlLogger->endCase($this);
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
	 * Login as admin.
	 *
	 * @return \GXSelenium\Engine\Emulator\Client
	 */
	protected function _admin()
	{
		$this->client->openBaseUrl(['logoff.php'])
		             ->openBaseUrl()
		             ->typeName('email_address', 'admin@shop.de')
		             ->typeName('password', '12345')
		             ->clickLinkText('Anmelden');

		return $this->client;
	}


	/**
	 * Logs an error and do a screenshot of the current screen.
	 *
	 * @param string $message Error message to log.
	 *
	 * @Todo Maybe merge code with _exceptionError method, seems to be redundant.
	 *
	 * @return $this|TestCase Same instance for chained method calls.
	 */
	protected function _error($message)
	{
		if($this->_isFailed()):
			return $this;
		endif;

		ob_start();
		debug_print_backtrace();
		$backtrace = ob_get_clean();

		$txt = $this->_getCaseName() . ' | ' . $this->_invokedBy() . ' | ' . $message . "\n" . $backtrace;
		$this->fileLogger->log($txt, 'errors');
		$this->fileLogger->screenshot($this->webDriver, implode('', array_map('ucfirst', explode(' ', $message))));
		echo "TestCaseFailed ..\n";
		$this->failed = true;

		return $this;
	}


	/**
	 * Logs a thrown exception and do a screenshot of the current screen.
	 *
	 * @param string     $message Error message of the current case.
	 * @param \Exception $e       Thrown exception.
	 *
	 * @return $this|TestCase Same instance for chained method calls.
	 */
	protected function _exceptionError($message, \Exception $e)
	{
		if($this->_isFailed()):
			return $this;
		endif;

		$screenMessage = implode('', array_map('ucfirst', explode(' ', $message)));
		$exceptionName = array_pop(explode('\\', get_class($e)));
		$screenName    = $this->_getCaseName() .
		                 ' | ' .
		                 $this->_invokedBy() .
		                 ' | ' .
		                 $screenMessage .
		                 ' | ' .
		                 $exceptionName;

		$txt = $this->_getCaseName() .
		       ' | ' .
		       $this->_invokedBy() .
		       ' | ' .
		       $message .
		       ' | ' .
		       $exceptionName .
		       " |\n" .
		       $e->getTraceAsString() .
		       "\n";

		$this->fileLogger->log($txt, 'errors');
		$this->fileLogger->screenshot($this->webDriver, str_replace(' ', '', $screenName));
		echo "TestCaseFailed ..\n";
		$this->failed = true;

		return $this;
	}


	private function _asd($message, \Exception $e = null)
	{
		$exceptionName = $e ? ' | ' . array_pop(explode('\\', get_class($e))) : null;
		$screenMessage = implode('', array_map('ucfirst', explode(' ', $message)));
		$screenName    = $this->_getCaseName() . ' | ' . $this->_invokedBy() . ' | ' . $screenMessage . $exceptionName;

		$this->fileLogger->screenshot($this->webDriver, str_replace(' ', '', $screenName));
		//		$this->fileLogger->log($txt, 'errors');
	}


	/**
	 * Wait the specified amount of time until the case will continue.
	 *
	 * @param string $expectedUrlSnippet Snippet of url to match before continue the case.
	 * @param int    $waitTimeout        (Optional) Amount of seconds to wait before the case fail. Default is 5.
	 * @param int    $delay              (Optional) Delay after the page is loaded. Default is 1.
	 * @param string $return             (Optional) Return value, when empty the client instance is returned.
	 *
	 * @return Client|TestCase|$this Either the same or the client instance, specified by the 3. argument.
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
		$return = $type ?: 'function';

		$debugBacktrace = debug_backtrace();
		if(!array_key_exists($deepness, $debugBacktrace)):
			throw new \UnexpectedValueException('no invokation with deepness "' . $deepness . '" found in backtrace');
		elseif(!array_key_exists($return, $debugBacktrace[2])):
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