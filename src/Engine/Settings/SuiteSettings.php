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

namespace GXSelenium\Engine\Settings;

use \Facebook\WebDriver\Remote\DesiredCapabilities;
use GXSelenium\Engine\TestCase;

/**
 * Class SuiteSettings
 * @package GXSelenium\Engine\Settings
 */
class SuiteSettings
{
	/**
	 * @var string
	 */
	private $branch = '';

	/**
	 * @var string
	 */
	private $buildNumber = '';

	/**
	 * Default capability is firefox.
	 *
	 * @var DesiredCapabilities
	 */
	private $capabilities;

	/**
	 * @var bool
	 */
	private $sendErrorMail = false;

	/**
	 * @var string
	 */
	private $sendMailFrom = '';

	/**
	 * @var string
	 */
	private $sendMailReplyTo = '';

	/**
	 * @var string
	 */
	private $sendMailTo = '';

	/**
	 * @var string
	 */
	private $version = '';

	/**
	 * @var string
	 */
	private $baseUrl = '';

	/**
	 * @var string
	 */
	private $webApp = '';

	/**
	 * @var string
	 */
	private $seleniumHost = 'http://localhost:4444/wd/hub';

	/**
	 * @var int
	 */
	private $scrollXOffset = 0;

	/**
	 * @var int
	 */
	private $scrollYOffset = 0;

	/**
	 * @var bool
	 */
	private $windowsMaximized = true;

	/**
	 * @var int
	 */
	private $implicitlyWait = 5;

	/**
	 * @var int
	 */
	private $pageLoadTimeout = 5;

	/**
	 * @var int
	 */
	private $scriptTimeout = 0;

	/**
	 * @var string
	 */
	private $loggingDirectoryName = '';

	/**
	 * @var string
	 */
	private $loggingDirectory;

	/**
	 * @var string
	 */
	private $screenShotDirectory = '';

	/**
	 * @var string
	 */
	private $currentTestCase;

	/**
	 * @var string
	 */
	private $suiteName = 'Selenium Testsuite';

	/**
	 * @var string
	 */
	private $dbHost = 'localhost';

	/**
	 * @var string
	 */
	private $dbUser = 'root';

	/**
	 * @var string
	 */
	private $dbPassword = '';

	/**
	 * @var string
	 */
	private $dbName = 'database_name';

	/**
	 * @var bool
	 */
	private $logStored = false;

	/**
	 * @var bool
	 */
	private $logDisplayed = true;

	/**
	 * @var string
	 */
	private $compareImageDir;

	/**
	 * @var string
	 */
	private $diffImageDir;

	/**
	 * @var bool
	 */
	private $compareImages = true;

	/**
	 * @var bool
	 */
	private $referenceImageSuite = false;


	/**
	 * Initialize the suite settings.
	 */
	public function __construct()
	{
		$this->capabilities     = DesiredCapabilities::firefox();
		$this->loggingDirectory = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'logs';
		$this->compareImageDir  = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'expectedImages';
		$this->diffImageDir     = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'diffImages';
	}


	/**
	 * @return string
	 */
	public function getBranch()
	{
		return $this->branch;
	}


	/**
	 * @param string $branch
	 */
	public function setBranch($branch)
	{
		$this->branch = $branch;
	}


	/**
	 * @return string
	 */
	public function getBuildNumber()
	{
		return $this->buildNumber;
	}


	/**
	 * @param string $buildNumber
	 */
	public function setBuildNumber($buildNumber)
	{
		$this->buildNumber = $buildNumber;
	}


	/**
	 * @return DesiredCapabilities
	 */
	public function getCapabilities()
	{
		return $this->capabilities;
	}


	/**
	 * @param DesiredCapabilities $capabilities
	 */
	public function setCapabilities($capabilities)
	{
		$this->capabilities = $capabilities;
	}


	/**
	 * @return boolean
	 */
	public function isSendErrorMail()
	{
		return $this->sendErrorMail;
	}


	/**
	 * @param boolean $sendErrorMail
	 */
	public function setSendErrorMail($sendErrorMail)
	{
		$this->sendErrorMail = (bool)$sendErrorMail;
		if((string)$sendErrorMail === 'false'):
			$this->sendErrorMail = false;
		endif;
	}


	/**
	 * @return string
	 */
	public function getSendMailFrom()
	{
		return $this->sendMailFrom;
	}


	/**
	 * @param string $sendMailFrom
	 */
	public function setSendMailFrom($sendMailFrom)
	{
		$this->sendMailFrom = $sendMailFrom;
	}


	/**
	 * @return string
	 */
	public function getSendMailReplyTo()
	{
		return $this->sendMailReplyTo;
	}


	/**
	 * @param string $sendMailReplyTo
	 */
	public function setSendMailReplyTo($sendMailReplyTo)
	{
		$this->sendMailReplyTo = $sendMailReplyTo;
	}


	/**
	 * @return string
	 */
	public function getSendMailTo()
	{
		return $this->sendMailTo;
	}


	/**
	 * @param string $sendMailTo
	 */
	public function setSendMailTo($sendMailTo)
	{
		$this->sendMailTo = $sendMailTo;
	}


	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}


	/**
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}


	/**
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}


	/**
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}


	/**
	 * @return string
	 */
	public function getWebApp()
	{
		return $this->webApp;
	}


	/**
	 * @param string $webApp
	 */
	public function setWebApp($webApp)
	{
		$this->webApp = $webApp;
	}


	/**
	 * @return string
	 */
	public function getSeleniumHost()
	{
		return $this->seleniumHost;
	}


	/**
	 * @param string $seleniumHost
	 */
	public function setSeleniumHost($seleniumHost)
	{
		$this->seleniumHost = $seleniumHost;
	}


	/**
	 * @return int
	 */
	public function getScrollXOffset()
	{
		return $this->scrollXOffset;
	}


	/**
	 * @param int $scrollXOffset
	 */
	public function setScrollXOffset($scrollXOffset)
	{
		$this->scrollXOffset = $scrollXOffset;
	}


	/**
	 * @return int
	 */
	public function getScrollYOffset()
	{
		return $this->scrollYOffset;
	}


	/**
	 * @param int $scrollYOffset
	 */
	public function setScrollYOffset($scrollYOffset)
	{
		$this->scrollYOffset = $scrollYOffset;
	}


	/**
	 * @return boolean
	 */
	public function isWindowsMaximized()
	{
		return $this->windowsMaximized;
	}


	/**
	 * @param boolean $windowsMaximized
	 */
	public function setWindowsMaximized($windowsMaximized)
	{
		$this->windowsMaximized = $windowsMaximized;
	}


	/**
	 * @return int
	 */
	public function getImplicitlyWait()
	{
		return $this->implicitlyWait;
	}


	/**
	 * @param int $implicitlyWait
	 */
	public function setImplicitlyWait($implicitlyWait)
	{
		$this->implicitlyWait = $implicitlyWait;
	}


	/**
	 * @return int
	 */
	public function getPageLoadTimeout()
	{
		return $this->pageLoadTimeout;
	}


	/**
	 * @param int $pageLoadTimeout
	 */
	public function setPageLoadTimeout($pageLoadTimeout)
	{
		$this->pageLoadTimeout = $pageLoadTimeout;
	}


	/**
	 * @return int
	 */
	public function getScriptTimeout()
	{
		return $this->scriptTimeout;
	}


	/**
	 * @param int $scriptTimeout
	 */
	public function setScriptTimeout($scriptTimeout)
	{
		$this->scriptTimeout = $scriptTimeout;
	}


	/**
	 * @return string
	 */
	public function getLoggingDirectoryName()
	{
		return $this->loggingDirectoryName;
	}


	/**
	 * @param string $loggingDirectoryName
	 */
	public function setLoggingDirectoryName($loggingDirectoryName)
	{
		$this->loggingDirectoryName = $loggingDirectoryName;
	}


	/**
	 * @return string
	 */
	public function getLoggingDirectory()
	{
		return $this->loggingDirectory;
	}


	/**
	 * @param string $loggingDirectory
	 */
	public function setLoggingDirectory($loggingDirectory)
	{
		$this->loggingDirectory = $loggingDirectory;
	}


	/**
	 * @return string
	 */
	public function getScreenShotDirectory()
	{
		return $this->screenShotDirectory;
	}


	/**
	 * @param string $screenShotDirectory
	 */
	public function setScreenShotDirectory($screenShotDirectory)
	{
		$this->screenShotDirectory = $screenShotDirectory;
	}


	/**
	 * @return TestCase
	 */
	public function getCurrentTestCase()
	{
		return $this->currentTestCase;
	}


	/**
	 * @param TestCase $currentCase
	 */
	public function setCurrentTestCase(TestCase $currentCase)
	{
		$this->currentTestCase = $currentCase;
	}


	/**
	 * @return string
	 */
	public function getSuiteName()
	{
		return $this->suiteName;
	}


	/**
	 * @param string $suiteName
	 */
	public function setSuiteName($suiteName)
	{
		$this->suiteName = $suiteName;
	}


	/**
	 * @return string
	 */
	public function getDbHost()
	{
		return $this->dbHost;
	}


	/**
	 * @param string $dbHost
	 */
	public function setDbHost($dbHost)
	{
		$this->dbHost = $dbHost;
	}


	/**
	 * @return string
	 */
	public function getDbUser()
	{
		return $this->dbUser;
	}


	/**
	 * @param string $dbUser
	 */
	public function setDbUser($dbUser)
	{
		$this->dbUser = $dbUser;
	}


	/**
	 * @return string
	 */
	public function getDbPassword()
	{
		return $this->dbPassword;
	}


	/**
	 * @param string $dbPassword
	 */
	public function setDbPassword($dbPassword)
	{
		$this->dbPassword = $dbPassword;
	}


	/**
	 * @return string
	 */
	public function getDbName()
	{
		return $this->dbName;
	}


	/**
	 * @param string $dbName
	 */
	public function setDbName($dbName)
	{
		$this->dbName = $dbName;
	}


	/**
	 * @return boolean
	 */
	public function isLogStored()
	{
		return $this->logStored;
	}


	/**
	 * @param boolean $logStored
	 */
	public function setLogStored($logStored)
	{
		$this->logStored = $logStored;
	}


	/**
	 * @return boolean
	 */
	public function isLogDisplayed()
	{
		return $this->logDisplayed;
	}


	/**
	 * @param boolean $logDisplayed
	 */
	public function setLogDisplayed($logDisplayed)
	{
		$this->logDisplayed = $logDisplayed;
	}


	/**
	 * @return string
	 */
	public function getCompareImageDir()
	{
		return $this->compareImageDir;
	}


	/**
	 * @param string $compareImageDir
	 */
	public function setCompareImageDir($compareImageDir)
	{
		$this->compareImageDir = $compareImageDir;
	}


	/**
	 * @return string
	 */
	public function getDiffImageDir()
	{
		return $this->diffImageDir;
	}


	/**
	 * @param string $diffImageDir
	 */
	public function setDiffImageDir($diffImageDir)
	{
		$this->diffImageDir = $diffImageDir;
	}
	
	
	/**
	 * @return boolean
	 */
	public function isCompareImages()
	{
		return $this->compareImages;
	}
	
	
	/**
	 * @param boolean $compareImages
	 */
	public function setCompareImages($compareImages)
	{
		$this->compareImages = $compareImages;
	}
	
	
	/**
	 * @return boolean
	 */
	public function isReferenceImageSuite()
	{
		return $this->referenceImageSuite;
	}
	
	
	/**
	 * @param boolean $referenceImageSuite
	 */
	public function setReferenceImageSuite($referenceImageSuite)
	{
		$this->referenceImageSuite = $referenceImageSuite;
	}
}