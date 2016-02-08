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

/**
 * Class SuiteSettings
 * @package GXSelenium\Engine\Settings
 */
class SuiteSettings
{
	/**
	 * @var string
	 */
	private $branch = 'default';

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
	private $shopVersion = '';

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
	private $casesNamespace = '';

	/**
	 * @var string
	 */
	private $seleniumHost = 'http://selenium:4444/wd/hub';

	/**
	 * @var int
	 */
	private $scrollXOffset = 0;

	/**
	 * @var int
	 */
	private $scrollYOffset = 0;

	/**
	 * @var string
	 */
	private $loggingDirectoryName = 'develop';

	/**
	 * @var string
	 */
	private $currentTestCase = '';

	/**
	 * @var string
	 */
	private $suiteName = 'Selenium Testsuite';

	private $dbHost = 'localhost';
	
	private $dbUser = 'root';
	
	private $dbPassword = '';
	
	private $dbName = 'database_name';


	/**
	 * Initialize the suite settings.
	 */
	public function __construct()
	{
		$this->capabilities = DesiredCapabilities::firefox();
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
		$this->sendErrorMail = $sendErrorMail;
	}


	/**
	 * @return string
	 */
	public function getShopVersion()
	{
		return $this->shopVersion;
	}


	/**
	 * @param string $shopVersion
	 */
	public function setShopVersion($shopVersion)
	{
		$this->shopVersion = $shopVersion;
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
	public function getCasesNamespace()
	{
		return $this->casesNamespace;
	}


	/**
	 * @param string $casesNamespace
	 */
	public function setCasesNamespace($casesNamespace)
	{
		$this->casesNamespace = $casesNamespace;
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
	public function getCurrentTestCase()
	{
		$caseNameArray = explode('\\', $this->currentTestCase);

		return $caseNameArray[count($caseNameArray) - 1];
	}


	/**
	 * @param string $currentCase
	 */
	public function setCurrentTestCase($currentCase)
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
}