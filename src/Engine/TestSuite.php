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

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriver;
use GXSelenium\Engine\Collections\TestCaseCollection;
use GXSelenium\Engine\Factory\SeleniumFactory;
use GXSelenium\Engine\Logger\FileLogger;
use GXSelenium\Engine\Logger\SqlLogger;
use GXSelenium\Engine\Settings\SuiteSettings;

/**
 * Class TestSuite
 * @package GXSelenium\TestSuites
 */
class TestSuite
{
	/**
	 * @var TestCaseCollection
	 */
	private $testCaseCollection;

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
	 * @var bool
	 */
	private $failed;

	/**
	 * @var FileLogger
	 */
	private $fileLogger;

	/**
	 * @var SqlLogger
	 */
	private $sqlLogger;


	/**
	 * Initialize the test suite.
	 *
	 * @param array|SuiteSettings $settings (Optional) Whether an assoc array or an instance of SuiteSettings.
	 *
	 * @throws \InvalidArgumentException When the passed argument is whether an array nor an instance of SuiteSettings.
	 */
	public function __construct($settings = null)
	{
		//echo "\nInitialize selenium test suite\n";
		$this->seleniumFactory = new SeleniumFactory($this);

		$this->setSettings($settings);
		$this->_initWebDriver();

		$this->fileLogger = $this->seleniumFactory->createFileLogger();
		$this->sqlLogger  = $this->seleniumFactory->createSqlLogger();
	}


	/**
	 * Tear down after usage.
	 */
	public function __destruct()
	{
		if($this->webDriver instanceof RemoteWebDriver):
			$this->webDriver->quit();
			$this->webDriver = null;
		endif;
	}


	public function run()
	{
		$this->sqlLogger->startSuite();
		foreach($this->testCaseCollection as $testCase):
			/** @var TestCase $testCase */
			$this->suiteSettings->setCurrentTestCase($testCase);
			$testCase->run();
		endforeach;
		$this->sqlLogger->endSuite();

		return $this;
	}


	/**
	 * Sets the suite settings.
	 * The passed argument can be an assoc array or an instance of SuiteSettings.
	 *
	 * @param array|SuiteSettings $settings Settings for the selenium test suite.
	 *
	 * @throws \InvalidArgumentException When the passed argument is whether an array nor an instance of SuiteSettings.
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	public function setSettings($settings)
	{
		if(is_array($settings)):
			$this->_setSettingsFromArray($settings);
		elseif($settings instanceof SuiteSettings):
			$this->suiteSettings = $settings;
		elseif(null === $settings):
			$this->suiteSettings = $this->seleniumFactory->createSuiteSettings();
		else:
			throw new \InvalidArgumentException('The passed argument have to be of type array or SuiteSettings');
		endif;

		return $this;
	}


	/**
	 * Sets new test cases to the test suite.
	 * The old case configuration gets lost!
	 *
	 * @param TestCase[] $testCaseArray
	 *
	 * @throws \UnexpectedValueException When created test case instance is not a child class of TestCase.
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	public function setTestCases(array $testCaseArray)
	{
		$cases = [];
		foreach($testCaseArray as $case):
			if($case instanceof TestCase):
				$cases[] = $case;
			else:
				try
				{
					$cases[] = $this->seleniumFactory->createTestCase($case);
				}
				catch(\UnexpectedValueException $e)
				{
					$this->webDriver->quit(); # Todo End of test suite! Handle abortion here.
					throw $e;
				}
			endif;
		endforeach;
		$this->testCaseCollection = $this->seleniumFactory->createTestCaseCollection($cases);
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
	 * Adds a new test case to the test suite.
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
	 * Returns the web driver instance.
	 *
	 * @return WebDriver
	 */
	public function getWebDriver()
	{
		return $this->webDriver;
	}


	/**
	 * Returns the test suite settings instance.
	 *
	 * @return SuiteSettings
	 */
	public function getSuiteSettings()
	{
		return $this->suiteSettings;
	}


	/**
	 * Initialize the remove web driver.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _initWebDriver()
	{
		$this->webDriver
			= RemoteWebDriver::create($this->suiteSettings->getSeleniumHost(), $this->suiteSettings->getCapabilities());

		return $this;
	}


	/**
	 * Sets the suite settings from an associative array.
	 *
	 * @param array $settings Settings array.
	 *
	 * @return $this|TestSuite Same instance for chained method calls.
	 */
	private function _setSettingsFromArray(array $settings)
	{
		echo "\nInitialize the test suite settings.\n";
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
				echo ($value instanceof DesiredCapabilities) ?
					'Set ' . lcfirst(str_replace('set', '', $setterName)) . ' = ' . $value->getBrowserName() . "\n" :
					'Set ' . lcfirst(str_replace('set', '', $setterName)) . ' = ' . $value . "\n";

				call_user_func([$suiteSettings, $setterName], $value);
			endif;
		endforeach;
		$this->suiteSettings = $suiteSettings;
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
	 */
	public function setFailed($failed)
	{
		$this->failed = $failed;
	}


	/**
	 * @return FileLogger
	 */
	public function getFileLogger()
	{
		return $this->fileLogger;
	}


	/**
	 * @return SqlLogger
	 */
	public function getSqlLogger()
	{
		return $this->sqlLogger;
	}
}