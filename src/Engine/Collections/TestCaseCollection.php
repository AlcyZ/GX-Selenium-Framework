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

namespace GXSelenium\Engine\Collections;

use GXSelenium\Engine\TestCase;
use Traversable;

/**
 * Class TestCaseCollection
 * @package GXSelenium\Engine\Collections
 */
class TestCaseCollection implements \Countable, \IteratorAggregate
{
	/**
	 * @var TestCase[]
	 */
	private $items = [];


	/**
	 * Initialize the test case collection.
	 *
	 * @param TestCase[] $testCaseArray Array which contains TestCase objects as elements.
	 */
	public function __construct(array $testCaseArray = array())
	{
		$this->_addItemsFromConstructor($testCaseArray);
	}


	/**
	 * Adds a new test case to the collection.
	 *
	 * @param TestCase $case
	 */
	public function add(TestCase $case)
	{
		$this->items[] = $case;
	}


	/**
	 * @param array $casesArray
	 *
	 * @return $this
	 */
	private function _addItemsFromConstructor(array $casesArray)
	{
		foreach($casesArray as $case):
			$this->_addCase($case);
		endforeach;

		return $this;
	}


	/**
	 * Adds a test case instance to the collection.
	 *
	 * @param TestCase $case
	 */
	private function _addCase(TestCase $case)
	{
		$this->items[] = $case;
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing <b>Iterator</b> or
	 * <b>Traversable</b>
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->items);
	}


	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 */
	public function count()
	{
		return count($this->items);
	}
}