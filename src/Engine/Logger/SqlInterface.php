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

use GXSelenium\Engine\TestCase;

interface SqlInterface
{
	/**
	 * Adds an empty log entry for the test suite.
	 *
	 * @return $this Same instance for chained method calls.
	 * @throws \Exception
	 */
	public function startSuite();


	/**
	 * Updates the empty log entry of the suite with data which are get while the suite was running.
	 *
	 * @return $this Same instance for chained method calls.
	 * @throws \Exception
	 */
	public function endSuite();


	/**
	 * Adds an empty log entry for the test case.
	 *
	 * @param TestCase $case
	 *
	 * @return $this Same instance for chained method calls.
	 * @throws \Exception
	 */
	public function startCase(TestCase $case);


	/**
	 * Updates the empty log entry of the case with data which are get while the case was running.
	 *
	 * @param TestCase $case
	 *
	 * @return $this Same instance for chained method calls.
	 * @throws \Exception
	 */
	public function endCase(TestCase $case);


	/**
	 * Adds a new log entry that the current test case is failed.
	 *
	 * @param string $message       Error message.
	 * @param string $errorUrl      Url where the error is occurred.
	 * @param string $screenshotUrl Url where the error screenshot is stored.
	 *
	 * @return $this Same instance for chained method calls.
	 */
	public function caseError($message, $errorUrl, $screenshotUrl);


	/**
	 * Adds a new log entry for a failed web driver initialization.
	 *
	 * @Todo Maybe add log entry in case table for error message.
	 *
	 * @return $this Same instance for chained method calls.
	 * @throws \Exception
	 */
	public function initError();
}