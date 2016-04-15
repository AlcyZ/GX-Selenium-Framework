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
use GXSelenium\Engine\Settings\SuiteSettings;
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
	protected $db;

	/**
	 * @var TestSuite
	 */
	protected $testSuite;

	/**
	 * @var SuiteSettings
	 */
	protected $suiteSettings;

	/**
	 * @var string
	 */
	protected $suitesTable = 'test_suites';

	/**
	 * @var int
	 */
	protected $lastSuiteId;

	/**
	 * @var Timer
	 */
	protected $suiteTimer;

	/**
	 * @var string
	 */
	protected $casesTable = 'test_cases';

	/**
	 * @var int
	 */
	protected $lastCaseId;

	/**
	 * @var Timer
	 */
	protected $caseTimer;

	/**
	 * @var string
	 */
	protected $caseErrorsTable = 'test_case_errors';


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
		$this->suiteSettings = $this->testSuite->getSuiteSettings();
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
			'test_case_id',
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
		])->execute();

		return $this;
	}
	

	/**
	 * Adds a new log entry for a failed web driver initialization.
	 *
	 * @Todo Maybe add log entry in case table for error message.
	 *
	 * @return $this Same instance for chained method calls.
	 * @throws \Exception
	 */
	public function initError()
	{
		$insert = $this->db->insert($this->suitesTable);
		$insert->columns($this->_getSuiteColumnsArray())->values([
			                                                         $this->_getSuiteValuesArray(false, true)
		                                                         ])->execute();

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
	protected function _getSuiteColumnsArray($update = false)
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
			'version',
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
	 * @param false|bool $update (Optional) Values for insert or update statement?
	 * @param bool       $error  (Optional) Sets instead of the pending value(0) the error status (2)
	 *
	 * @return array
	 */
	protected function _getSuiteValuesArray($update = false, $error = false)
	{
		if($update):
			return [
				($this->testSuite->isFailed()) ? 2 : 1,
				date('Y-m-d H:i:s'),
				$this->suiteTimer->time()
			];
		endif;

		return [
			$this->suiteSettings->getBuildNumber(),
			$this->suiteSettings->getSuiteName(),
			$this->suiteSettings->getVersion(),
			$this->suiteSettings->getBranch(),
			$error ? 2 : 0,
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
	protected function _getCaseColumnsArray($update = false)
	{
		if($update):
			return [
				'status',
				'end',
				'passed_time',
			];
		endif;

		return [
			'test_suite_id',
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
	protected function _getCaseValuesArray(TestCase $case, $update = false)
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