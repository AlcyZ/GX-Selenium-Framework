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

use Alcys\Core\Db\Service\AlcysDb;
use GXSelenium\Engine\TestCase;
use GXSelenium\Engine\TestSuite;
use GXSelenium\Engine\Timer\Timer;

/**
 * Class SqlLogger
 * @package GXSelenium\Engine\Logger
 */
class SqlLogger implements SqlInterface
{
	/**
	 * @var AlcysDb
	 */
	private $db;

	/**
	 * @var TestSuite
	 */
	private $testSuite;

	/**
	 * @var string
	 */
	private $suitesTable = 'suites';

	/**
	 * @var int
	 */
	private $lastSuiteId;

	/**
	 * @var Timer
	 */
	private $suiteTimer;

	/**
	 * @var string
	 */
	private $casesTable = 'cases';

	/**
	 * @var int
	 */
	private $lastCaseId;

	/**
	 * @var Timer
	 */
	private $caseTimer;

	/**
	 * @var string
	 */
	private $caseErrorsTable = 'case_errors';


	/**
	 * Initialize the sql logger.
	 *
	 * @param AlcysDb   $db
	 * @param TestSuite $testSuite
	 */
	public function __construct(AlcysDb $db, TestSuite $testSuite)
	{
		$this->db        = $db;
		$this->testSuite = $testSuite;
	}


	/**
	 * Adds an empty log entry for the test suite.
	 *
	 * @return $this Same instance for chained method calls.
	 * @throws \Exception
	 */
	public function startSuite()
	{
		$insert = $this->_initSuiteTimer()->db->insert($this->suitesTable);
		$insert->columns($this->_getSuiteColumnsArray())->values($this->_getSuiteValuesArray());

		$this->lastSuiteId = $insert->execute();

		return $this;
	}


	/**
	 * Updates the empty log entry of the suite with data which are get while the suite was running.
	 *
	 * @return $this Same instance for chained method calls.
	 * @throws \Exception
	 */
	public function endSuite()
	{
		$update = $this->db->update($this->suitesTable);
		$update->columns($this->_getSuiteColumnsArray(true))
		       ->values($this->_getSuiteValuesArray(true))
		       ->where($update->condition()->equal('id', $this->lastSuiteId));

		$update->execute();

		return $this;
	}


	/**
	 * Adds an empty log entry for the test case.
	 *
	 * @param TestCase $case
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function startCase(TestCase $case)
	{
		$insert = $this->_initCaseTimer()->db->insert($this->casesTable);
		$insert->columns($this->_getCaseColumnsArray())->values($this->_getCaseValuesArray($case));

		$this->lastCaseId = $insert->execute();

		return $this;
	}


	/**
	 * Updates the empty log entry of the case with data which are get while the case was running.
	 *
	 * @param TestCase $case
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function endCase(TestCase $case)
	{
		$update = $this->db->update($this->casesTable);
		$update->columns($this->_getCaseColumnsArray(true))
		       ->values($this->_getCaseValuesArray($case, true))
		       ->where($update->condition()->equal('id', $this->lastCaseId));

		$update->execute();

		return $this;
	}


	/**
	 * Adds a new log entry that the current test case is failed.
	 *
	 * @param string $message       Error message.
	 * @param string $errorUrl      Url where the error is occurred.
	 * @param string $screenshotUrl Url where the error screenshot is stored.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function caseError($message, $errorUrl, $screenshotUrl)
	{
		$insert = $this->db->insert($this->caseErrorsTable);
		$insert->columns([
			                 'case_id',
			                 'error_message',
			                 'screenshot_url',
			                 'error_url',
			                 'time'
		                 ])->values([
			                            $this->lastCaseId,
			                            $message,
			                            $screenshotUrl,
			                            $errorUrl,
			                            date('Y-m-d H:i:s')
		                            ]);

		return $this;
	}

	################################### helper methods for the suite table #############################################
	/**
	 * Returns an array with column names for the suite table.
	 *
	 * @param false|bool $update Columns for insert or update statement?
	 *
	 * @return array
	 */
	private function _getSuiteColumnsArray($update = false)
	{
		if($update):
			return [
				'status',
				'end',
				'passed_time'
			];
		endif;

		return [
			'build_number',
			'suite_name',
			'shop_version',
			'branch',
			'status',
			'begin',
			'end',
			'passed_time'
		];
	}


	/**
	 * Returns an array with values for the suite log entry.
	 *
	 * @param false|bool $update Values for insert or update statement?
	 *
	 * @return array
	 */
	private function _getSuiteValuesArray($update = false)
	{
		if($update):
			return [
				($this->testSuite->isFailed()) ? 2 : 1,
				date('Y-m-d H:i:s'),
				$this->suiteTimer->time()
			];
		endif;

		return [
			$this->testSuite->getSuiteSettings()->getBuildNumber(),
			$this->testSuite->getSuiteSettings()->getSuiteName(),
			$this->testSuite->getSuiteSettings()->getShopVersion(),
			$this->testSuite->getSuiteSettings()->getBranch(),
			0,
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
			0.00
		];
	}


	/**
	 * Initialize the timer instance for suite log entries.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	private function _initSuiteTimer()
	{
		if(null === $this->suiteTimer):
			$this->suiteTimer = new Timer();
		else:
			$this->suiteTimer->reset();
		endif;

		return $this;
	}


	################################### helper methods for the cases table ##############
	################################
	/**
	 * Returns an array with column names for the case table.
	 *
	 * @param false|bool $update Columns for insert or update statement?
	 *
	 * @return array
	 */
	private function _getCaseColumnsArray($update = false)
	{
		if($update):
			return [
				'status',
				'end',
				'passed_time',
			];
		endif;

		return [
			'suite_id',
			'name',
			'status',
			'begin',
			'end',
			'passed_time',
		];
	}


	/**
	 * Returns an array with values for the case log entry.
	 *
	 * @param TestCase   $case   Current test case instance.
	 * @param false|bool $update Values for insert or update statement?
	 *
	 * @return array
	 */
	private function _getCaseValuesArray(TestCase $case, $update = false)
	{
		if($update):
			return [
				($case->_isFailed()) ? 2 : 1,
				date('Y-m-d H:i:s'),
				$this->caseTimer->time()
			];
		endif;

		return [
			$this->lastSuiteId,
			$case->getCaseName(),
			0,
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
			0.0
		];
	}


	/**
	 * Initialize the timer instance for case log entries.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	private function _initCaseTimer()
	{
		if(null === $this->caseTimer):
			$this->caseTimer = new Timer();
		else:
			$this->caseTimer->reset();
		endif;

		return $this;
	}

}