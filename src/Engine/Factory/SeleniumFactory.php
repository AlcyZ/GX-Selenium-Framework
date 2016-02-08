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

namespace GXSelenium\Engine\Factory;

use Alcys\Core\Db\Service\AlcysDb;
use GXSelenium\Engine\Collections\TestCaseCollection;
use GXSelenium\Engine\Emulator\Client;
use GXSelenium\Engine\Logger\FileLogger;
use GXSelenium\Engine\Logger\SqlLogger;
use GXSelenium\Engine\Logger\SqlNull;
use GXSelenium\Engine\Provider\ElementProvider;
use GXSelenium\Engine\Settings\SuiteSettings;
use GXSelenium\Engine\TestCase;
use GXSelenium\Engine\TestSuite;

class SeleniumFactory
{
	/**
	 * @var TestSuite
	 */
	private $testSuite;

	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var FileLogger
	 */
	private $fileLogger;

	/**
	 * @var SqlLogger
	 */
	private $sqlLogger;

	/**
	 * @var ElementProvider
	 */
	private $elementProvider;


	/**
	 * Initialize the selenium factory.
	 *
	 * @param \GXSelenium\Engine\TestSuite              $testSuite
	 * @param \GXSelenium\Engine\Emulator\Client|null   $client
	 * @param \GXSelenium\Engine\Logger\FileLogger|null $fileLogger
	 */
	public function __construct(TestSuite $testSuite, Client $client = null, FileLogger $fileLogger = null)
	{
		$this->testSuite  = $testSuite;
		$this->client     = $client;
		$this->fileLogger = $fileLogger;
	}


	/**
	 * Creates a new test case collection instance.
	 *
	 * @param TestCase[] $testCaseArray
	 *
	 * @return TestCaseCollection
	 */
	public function createTestCaseCollection(array $testCaseArray = [])
	{
		return new TestCaseCollection($testCaseArray);
	}


	/**
	 * Creates a new test case instance.
	 * The test case must be a child class of the abstract TestCase class.
	 *
	 * @param string $testCaseName Name of test case, without 'Case'-suffix namespace (stored in suite settings).
	 *
	 * @return TestCase
	 * @throws \UnexpectedValueException Either when test case class not exists or is not a child class of TestCase.
	 */
	public function createTestCase($testCaseName)
	{
		$namespace = $this->testSuite->getSuiteSettings()->getCasesNamespace();
		$caseName  = ucfirst($testCaseName) . 'Case';
		$testCase  = $namespace . '\\' . $caseName;

		if(!class_exists($testCase)):
			throw new \UnexpectedValueException('Class "' . $testCase . '" not found!');
		endif;

		$case = new $testCase($this->testSuite, $this->createClientEmulator());
		if(!$case instanceof TestCase):
			throw new \UnexpectedValueException('The class "'
			                                    . $testCase
			                                    . '" must be a child class of the '
			                                    . 'main test case class!');
		endif;

		return $case;
	}


	/**
	 * Returns an instance of a client emulator.
	 * At multiple calls, the same client instance is returned.
	 *
	 * @return Client
	 */
	public function createClientEmulator()
	{
		if(null === $this->client):
			$this->client = new Client($this->testSuite, $this->createElementProvider());
		endif;

		return $this->client;
	}


	/**
	 * Returns an instance of a file logger.
	 * At multiple calls, the same logging instance is returned.
	 *
	 * @return FileLogger
	 */
	public function createFileLogger()
	{
		if(null === $this->fileLogger):
			$this->fileLogger = new FileLogger($this->testSuite->getSuiteSettings()->getLoggingDirectoryName());
		endif;

		return $this->fileLogger;
	}


	/**
	 * Returns an instance of a sql logger.
	 *
	 * @return SqlLogger
	 */
	public function createSqlLogger()
	{
		if(null === $this->sqlLogger):
			try
			{
				$db              =
					new AlcysDb('mysql:host='
					            . $this->testSuite->getSuiteSettings()->getDbHost()
					            . ';dbname='
					            . $this->testSuite->getSuiteSettings()->getDbName(),
					            $this->testSuite->getSuiteSettings()->getDbUser(),
					            $this->testSuite->getSuiteSettings()->getDbPassword());
				$this->sqlLogger = new SqlLogger($db, $this->testSuite);
			}
			catch(\PDOException $e)
			{
				echo "\nInvalid db credentials .. database logging deactivated\n";
				$this->sqlLogger = new SqlNull();
			}
		endif;

		return $this->sqlLogger;
	}


	/**
	 * Returns an instance of a element provider.
	 * At multiple calls, the same element provider instance is returned.
	 *
	 * @return ElementProvider
	 */
	public function createElementProvider()
	{
		if(null === $this->elementProvider):
			$this->elementProvider = new ElementProvider($this->testSuite);
		endif;

		return $this->elementProvider;
	}


	/**
	 * Creates a new instance of suite settings.
	 *
	 * @return SuiteSettings
	 */
	public function createSuiteSettings()
	{
		return new SuiteSettings();
	}
}