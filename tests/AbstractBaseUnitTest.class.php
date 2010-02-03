<?php
/**
 * @package modules.useractionlogger.tests
 */
abstract class useractionlogger_tests_AbstractBaseUnitTest extends useractionlogger_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->resetDatabase();
	}
}