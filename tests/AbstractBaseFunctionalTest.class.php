<?php
/**
 * @package modules.useractionlogger.tests
 */
abstract class useractionlogger_tests_AbstractBaseFunctionalTest extends useractionlogger_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->loadSQLResource('functional-test.sql', true, false);
	}
}