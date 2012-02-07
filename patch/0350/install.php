<?php
/**
 * useractionlogger_patch_0350
 * @package modules.useractionlogger
 */
class useractionlogger_patch_0350 extends patch_BasePatch
{ 
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		useractionlogger_ModuleService::getInstance()->addPurgeLogTask();
	}
}