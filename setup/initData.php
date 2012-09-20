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
}