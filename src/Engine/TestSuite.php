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

use Facebook\WebDriver\Exception\WebDriverCurlException;
use Facebook\WebDriver\JavaScriptExecutor;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriver;
use GXSelenium\Engine\Collections\TestCaseCollection;
use GXSelenium\Engine\Factory\SeleniumFactory;
use GXSelenium\Engine\Logger\FileLogger;
use GXSelenium\Engine\Logger\SqlLogger;
use GXSelenium\Engine\Settings\SuiteSettings;

class TestSuite
{
	/**
	 * @var TestCaseCollection
	 */
	private $testCaseCollection;

	/**
	 * @var array
	 */
	private $cases = [];

	/**
	 * @var SuiteSettings
	 */
	private $suiteSettings;

	/**
	 * @var WebDriver
	 */
	private $webDriver;

	/**
	 * @var SeleniumFactory
	 */
	private $seleniumFactory;

	/**
	 * @var FileLogger
	 */
	private $fileLogger;

	/**
	 * @var SqlLogger
	 */
	private $sqlLogger;

	/**
	 * @var bool
	 */
	private $failed = false;

	/**
	 * @var bool
	 */
	private $errorMailSend = false;

	/**
	 * @var string
	 */
	private $errorMessages = '';


	/**
	 * TestSuite constructor.
	 *
	 * @param SuiteSettings|array|null $settings
	 */
	public function __construct($settings = null)
	{
		$this->_initSeleniumFactory()
		     ->_initSuiteSettings($settings)
		     ->_initTestCaseCollection()
		     ->_initWebDriver()
		     ->_initSqlLogger()
		     ->_initFileLogger();
	}


	/**
	 * Tear down after usage.
	 */
	public function __destruct()
	{
		return $this->_closeWebDriver();
	}


	/**
	 * Closes the WebDriver session.
	 *
	 * @return $this
	 */
	private function _closeWebDriver()
	{
		if($this->webDriver instanceof RemoteWebDriver):
			$this->output('Close WebDriver session');
			$this->webDriver->close()->quit();
			$this->webDriver = null;
		endif;

		return $this;
	}


	/**
	 * Starts and runs the test suite.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	public function run()
	{
		$this->_applyMaximizedWindowSetting()
		     ->_applyImplicitlyWaitTimeoutSetting()
		     ->_applyPageLoadTimeoutSetting()
		     ->_applyScriptTimeoutSetting()
		     ->_addCasesToCollection()
		     ->_determineSuiteType()->sqlLogger->startSuite();

		foreach($this->testCaseCollection as $testCase):
			/** @var TestCase $testCase */
			$this->suiteSettings->setCurrentTestCase($testCase);
			$testCase->run();
		endforeach;
		$this->sqlLogger->endSuite();

		if(!$this->errorMailSend && $this->failed && $this->suiteSettings->isSendErrorMail()):
			$this->_sendErrorMail();
		endif;

		if($this->isFailed() && $this->getSuiteSettings()->isReferenceImageSuite()):
			$client = $this->seleniumFactory->createClientEmulator();
			$this->_truncateDirectory($client->getExpectedImagesDirectory());
		endif;

		$this->_closeWebDriver();

		return $this;
	}
	

	/**
	 * Truncates the given directory.
	 *
	 * @param string $directory Path to expected directory.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _truncateDirectory($directory)
	{
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory,
		                                                                           \RecursiveDirectoryIterator::SKIP_DOTS),
		                                           \RecursiveIteratorIterator::CHILD_FIRST);
		foreach($iterator as $fileInfo):
			$fileInfo->isDir() ? rmdir($fileInfo) : unlink($fileInfo);
		endforeach;

		return $this;
	}

	#################################### helper methods to add test cases ##############################################
	/**
	 * Push a new case in the case array property.
	 * Before the suite starts, all case values from the array are transformed into TestCase objects
	 * and used in the test suite.
	 *
	 * @param string $caseName Name of the test case.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	public function pushTestCase($caseName)
	{
		$this->cases[] = rtrim($caseName);

		return $this;
	}


	/**
	 * Push an array with case names in the case array property.
	 * Before the suite starts, all case values from the array are transformed into TestCase objects
	 * and used in the test suite.
	 *
	 * @param array $caseNamesArray Array which contains the case names.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	public function pushTestCases(array $caseNamesArray)
	{
		foreach($caseNamesArray as $case):
			$this->pushTestCase($case);
		endforeach;

		return $this;
	}


	/**
	 * Adds a new test case to the test suites collection.
	 *
	 * @param TestCase $case Test case instance.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	public function addTestCase(TestCase $case)
	{
		$this->testCaseCollection->add($case);

		return $this;
	}


	/**
	 * Adds all test cases from the passed array the the test suites collection.
	 *
	 * @param array $caseArray
	 *
	 * @return $this
	 */
	public function addTestCases(array $caseArray)
	{
		foreach($caseArray as $case):
			$this->addTestCase($case);
		endforeach;

		return $this;
	}


	/**
	 * Sets a new test case collection to the test suite.
	 * The old case configuration gets lost!
	 *
	 * @param TestCaseCollection $collection
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	public function setTestCaseCollection(TestCaseCollection $collection)
	{
		$this->testCaseCollection = $collection;

		return $this;
	}


	/**
	 * Adds the test cases from the ::case property to the collection.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _addCasesToCollection()
	{
		foreach($this->cases as $caseName):
			$this->testCaseCollection->add($this->seleniumFactory->createTestCase($caseName));
		endforeach;

		return $this;
	}


	########################################## other helper methods ####################################################
	/**
	 * Sends an mail with the error messages.
	 *
	 * The receiver and the sender is set in the suite settings.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _sendErrorMail()
	{
		$from  = $this->suiteSettings->getSendMailFrom();
		$to    = $this->suiteSettings->getSendMailTo();
		$reply = $this->suiteSettings->getSendMailReplyTo();

		if($to === '' || $reply === '' || $from === ''):
			$this->output('Invalid E-Mail credentials, not possible to send the error mail');

			return $this;
		endif;

		$subject = '[SeleniumTest] Test fehlgeschlagen, Branch: ' . $this->suiteSettings->getBranch();
		$header  = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $reply . "\r\n"
		           . 'Content-Type: text/plain; charset=UTF-8"';
		mail($to, $subject, $this->errorMessages, $header);
		$this->output('Error E-Mail send!');
		$this->errorMailSend = true;

		return $this;
	}


	/**
	 * Adds a message to the error message string.
	 *
	 * @param $errorMessage
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	public function addErrorMessage($errorMessage)
	{
		$this->errorMessages .= $errorMessage . "\n";

		return $this;
	}

	######################################### init methods (return $this) ##############################################
	/**
	 * Initialize the test case collection.
	 *
	 * @param array $testCaseArray
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _initTestCaseCollection(array $testCaseArray = [])
	{
		if(null === $this->testCaseCollection):
			$this->testCaseCollection = $this->seleniumFactory->createTestCaseCollection($testCaseArray);
		endif;

		return $this;
	}


	/**
	 * Initialize the selenium factory.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _initSeleniumFactory()
	{
		$this->seleniumFactory = new SeleniumFactory($this);

		return $this;
	}


	/**
	 * Initialize the suite settings.
	 * The passed argument can be an assoc array or an instance of SuiteSettings.
	 *
	 * @param array|SuiteSettings $settings Settings for the selenium test suite.
	 *
	 * @throws \InvalidArgumentException When the passed argument is whether an array nor an instance of SuiteSettings.
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _initSuiteSettings($settings)
	{
		if(null === $settings):

			$this->suiteSettings = $this->seleniumFactory->createSuiteSettings();

		elseif($settings instanceof SuiteSettings):

			$this->suiteSettings = $settings;

		elseif(is_array($settings)):

			$this->_initSuiteSettingsFromArray($settings);

		else:

			throw new \InvalidArgumentException('The passed argument have to be of type array or SuiteSettings');

		endif;

		return $this;
	}


	/**
	 * Initialize the suite settings from an associative array.
	 *
	 * @param array $settings Settings array.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _initSuiteSettingsFromArray(array $settings)
	{
		$this->output("\n" . 'Initialize the test suite settings.');
		$suiteSettings = $this->seleniumFactory->createSuiteSettings();
		foreach($settings as $key => $value):
			if($key === 'browser'):
				$setterName = 'setCapabilities';
				switch($value):
					case 'firefox':
						$value = DesiredCapabilities::firefox();
						break;
					case 'chrome':
						$value = DesiredCapabilities::chrome();
						break;
				endswitch;
			else:
				$setterName = 'set' . ucfirst($key);
			endif;

			if(method_exists($suiteSettings, $setterName)):
				if($value instanceof DesiredCapabilities):
					$message = 'Set ' . lcfirst(str_replace('set', '', $setterName)) . ' = ' . $value->getBrowserName();
				else:
					$message = 'Set ' . lcfirst(str_replace('set', '', $setterName)) . ' = ' . $value;
				endif;
				$this->output($message);

				call_user_func([$suiteSettings, $setterName], $value);
			endif;
		endforeach;
		$this->suiteSettings = $suiteSettings;

		return $this;
	}


	/**
	 * Initialize the remove web driver.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _initWebDriver()
	{
		try
		{
			if(null === $this->webDriver):
				$this->webDriver = RemoteWebDriver::create($this->suiteSettings->getSeleniumHost(),
				                                           $this->suiteSettings->getCapabilities());
			endif;
		}
		catch(WebDriverCurlException $e)
		{
			$this->_initSqlLogger();
			$this->sqlLogger->initError();
			exit("\n\e[41mFailed to initialize the remote web driver, there is may be a problem with the browser driver.\n"
			     . $e->getMessage() . "\e[0m\n\n");
		}
		catch(\Exception $e)
		{
			$this->_initSqlLogger();
			$this->sqlLogger->initError();
			exit("\n\e[41mException while the web driver is instantiating.\n" . $e->getMessage() . "\e[0m\n\n");
		}

		return $this;
	}


	/**
	 * Initialize the sql logger if not already set and return it.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _initSqlLogger()
	{
		if(null === $this->sqlLogger):
			$this->sqlLogger = $this->seleniumFactory->createSqlLogger();
		endif;

		return $this;
	}


	/**
	 * Initialize the file logger if not already set and return it.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _initFileLogger()
	{
		if(null === $this->fileLogger):
			$this->fileLogger = $this->seleniumFactory->createFileLogger();
		endif;

		return $this;
	}
	

	/**
	 * Displays a message on the console.
	 *
	 * @param string $message
	 */
	public function output($message)
	{
		$currentTestCase = $this->suiteSettings->getCurrentTestCase();
		if($currentTestCase):
			$currentTestCase->output($message);
		else:
			echo $message . "\n";
		endif;
	}

	################################### helper methods to apply settings ###############################################
	/**
	 * Checks if the maximized window setting is set. If true, apply it.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _applyMaximizedWindowSetting()
	{
		if($this->suiteSettings->isWindowsMaximized()):
			$this->output('Maximize web driver window!');
			$this->webDriver->manage()->window()->maximize();
		endif;

		return $this;
	}
	

	/**
	 * Applies the implicitly wait timeout setting.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _applyImplicitlyWaitTimeoutSetting()
	{
		$implicitlyWait = $this->suiteSettings->getImplicitlyWait();
		if((int)$implicitlyWait !== 0):
			$this->output('Set implicitly wait setting to ' . $implicitlyWait . ' seconds.');
			$this->webDriver->manage()->timeouts()->implicitlyWait($implicitlyWait);
		endif;

		return $this;
	}
	

	/**
	 * Applies the page load timeout setting.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _applyPageLoadTimeoutSetting()
	{
		$pageLoadTimeout = $this->suiteSettings->getPageLoadTimeout();
		if((int)$pageLoadTimeout !== 0):
			$this->output('Set page load timeout setting to ' . $pageLoadTimeout . ' seconds.');
			$this->webDriver->manage()->timeouts()->pageLoadTimeout($pageLoadTimeout);
		endif;

		return $this;
	}
	
	
	/**
	 * Applies the script timeout setting.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _applyScriptTimeoutSetting()
	{
		$scriptTimeout = $this->suiteSettings->getScriptTimeout();
		if((int)$scriptTimeout !== 0):
			$this->output('Set script timeout setting to ' . $scriptTimeout . ' seconds.');
			$this->webDriver->manage()->timeouts()->setScriptTimeout($scriptTimeout);
		endif;

		return $this;
	}


	/**
	 * Determine whether the test suite should create reference images
	 * or should compare with already created reference images.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _determineSuiteType()
	{
		if($this->getSuiteSettings()->isForceReferenceImageSuite())
		{
			$this->getSuiteSettings()->setReferenceImageSuite(true);
			$this->output('Running reference images test suite [forced]');

			return $this;
		}

		$client = $this->seleniumFactory->createClientEmulator();
		if(!(new \FilesystemIterator($client->getExpectedImagesDirectory()))->valid())
		{
			$this->getSuiteSettings()->setReferenceImageSuite(true);
			$this->output('Running reference images test suite');
		}
		else
		{
			$this->getSuiteSettings()->setReferenceImageSuite(false);
			$this->output('Running compare images test suite');
		}

		return $this;
	}
	
	########################################## getter and setter #######################################################
	/**
	 * @return SuiteSettings
	 */
	public function getSuiteSettings()
	{
		return $this->suiteSettings;
	}


	/**
	 * @return WebDriver|JavaScriptExecutor
	 */
	public function getWebDriver()
	{
		return $this->webDriver;
	}


	/**
	 * @return FileLogger
	 */
	public function getFileLogger()
	{
		return $this->fileLogger;
	}


	/**
	 * @param FileLogger $fileLogger
	 */
	public function setFileLogger($fileLogger)
	{
		$this->fileLogger = $fileLogger;
	}


	/**
	 * @return SqlLogger
	 */
	public function getSqlLogger()
	{
		return $this->sqlLogger;
	}


	/**
	 * @param SqlLogger $sqlLogger
	 */
	public function setSqlLogger($sqlLogger)
	{
		$this->sqlLogger = $sqlLogger;
	}


	/**
	 * @return boolean
	 */
	public function isFailed()
	{
		return $this->failed;
	}


	/**
	 * @param boolean $failed
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	public function setFailed($failed)
	{
		$this->failed = $failed;

		return $this;
	}
}