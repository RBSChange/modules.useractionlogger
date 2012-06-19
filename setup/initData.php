<?php
/**
 * @package modules.useractionlogger.setup
 */
class useractionlogger_Setup extends object_InitDataSetup
{
	public function install()
	{
		$this->addInjectionInProjectConfiguration('UserActionLoggerService', 'useractionlogger_LoggerService');
		
		useractionlogger_ModuleService::getInstance()->addPurgeLogTask();
		useractionlogger_ModuleService::getInstance()->importAllInitScript();
	}

	/**
	 * @return array<string>
	 */
	public function getRequiredPackages()
	{
		// Return an array of packages name if the data you are inserting in
		// this file depend on the data of other packages.
		// Example:
		// return array('modules_website', 'modules_users');
		return array();
	}
}