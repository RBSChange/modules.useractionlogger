<?php
/**
 * @package modules.useractionlogger.tests
 */
abstract class useractionlogger_tests_AbstractBaseIntegrationTest extends useractionlogger_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->loadSQLResource('integration-test.sql', true, false);
	}
}