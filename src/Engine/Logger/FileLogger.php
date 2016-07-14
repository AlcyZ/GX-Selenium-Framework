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

namespace GXSelenium\Engine\Logger;

use Facebook\WebDriver\WebDriver;
use GXSelenium\Engine\Settings\SuiteSettings;

/**
 * Class FileLogger
 * @package GXSelenium\Engine\Logger
 */
class FileLogger
{
	/**
	 * @var SuiteSettings
	 */
	private $suiteSettings;

	/**
	 * @var string
	 */
	private $logsDir;

	/**
	 * @var string
	 */
	private $screenshotDir;

	/**
	 * @var resource
	 */
	private $logResource;


	/**
	 * Initialize the file logger.
	 *
	 * @param SuiteSettings $settings
	 */
	public function __construct(SuiteSettings $settings)
	{
		$this->suiteSettings = $settings;

		$loggingDirectory     = $this->suiteSettings->getLoggingDirectory();
		$loggingDirectoryPath = ($loggingDirectory !== '') ? $loggingDirectory : dirname(dirname(dirname(__DIR__)))
		                                                                         . DIRECTORY_SEPARATOR . 'logs';

		$buildNumber     = $this->suiteSettings->getBuildNumber();
		$buildNumberPath = ($buildNumber !== '') ? DIRECTORY_SEPARATOR . $buildNumber : '';

		$loggingDirectoryName     = $this->suiteSettings->getLoggingDirectoryName();
		$loggingDirectoryNamePath = ($loggingDirectoryName !== '') ? DIRECTORY_SEPARATOR . $loggingDirectoryName : '';

		$this->logsDir = $loggingDirectoryPath . $buildNumberPath . $loggingDirectoryNamePath;

		$this->screenshotDir = $this->logsDir . DIRECTORY_SEPARATOR . 'screenshots';
	}


	/**
	 * Logs a message in an file
	 *
	 * @param string $message   Message to log.
	 * @param string $file      Name of logging file.
	 * @param string $extension Extension of logging file (without dot).
	 *
	 * @return $this|FileLogger Same instance for chained method calls.
	 */
	public function log($message, $file, $extension = 'txt')
	{
		$this->_createLoggingDirIfNotExists()->_prepareLogFile($file, $extension)->_logMessage($message);
		fclose($this->logResource);

		return $this;
	}


	public function screenshot(WebDriver $webDriver, $case = 'undefined')
	{
		$this->_createScreenShotDirIfNotExists();
		$screenName = date('d|m|y|H|i|s') . '|' . $case . '.png';

		file_put_contents($this->screenshotDir . DIRECTORY_SEPARATOR . $screenName, $webDriver->takeScreenshot());

		$buildNumber     = $this->suiteSettings->getBuildNumber();
		$buildNumberPath = ($buildNumber !== '') ? $buildNumber . DIRECTORY_SEPARATOR : '';

		$loggingDirectoryName     = $this->suiteSettings->getLoggingDirectoryName();
		$loggingDirectoryNamePath = ($loggingDirectoryName !== '') ? $loggingDirectoryName . DIRECTORY_SEPARATOR : '';

		return rtrim($buildNumberPath . $loggingDirectoryNamePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
		       . 'screenshots' . DIRECTORY_SEPARATOR . $screenName;
	}


	/**
	 * Creates the screenshot directory if not exits.
	 *
	 * @return $this|FileLogger Same instance for chained method calls.
	 */
	private function _createScreenShotDirIfNotExists()
	{
		$this->_createLoggingDirIfNotExists();
		(!is_dir($this->screenshotDir)) ? mkdir($this->screenshotDir) : null;

		return $this;
	}


	/**
	 * Creates the logging directory if not exits.
	 *
	 * @return $this|FileLogger Same instance for chained method calls.
	 */
	private function _createLoggingDirIfNotExists()
	{
		(!is_dir($this->logsDir)) ? mkdir($this->logsDir, 0777, true) : null;

		return $this;
	}


	/**
	 * Prepare the logging file and store the resource in an internal property.
	 *
	 * @param string $file      File name.
	 * @param string $extension File extension (without dot).
	 *
	 * @return $this|FileLogger Same instance for chained method calls.
	 */
	private function _prepareLogFile($file, $extension)
	{
		$logFile = $this->logsDir . DIRECTORY_SEPARATOR . $file . '.' . $extension;
		(!file_exists($logFile)) ? touch($logFile) : null;

		$this->logResource = fopen($logFile, 'a+');

		return $this;
	}


	/**
	 * Write the message in the logging file.
	 *
	 * @param string $message Message to write.
	 *
	 * @return $this|FileLogger Same instance for chained method calls.
	 */
	private function _logMessage($message)
	{
		$txt = date('d.m.Y H:i:s') . ' » ' . $message . "\n";
		fwrite($this->logResource, $txt);

		return $this;
	}
}