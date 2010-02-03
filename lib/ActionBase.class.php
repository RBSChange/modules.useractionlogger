<?php
/**
 * @package modules.useractionlogger.lib
 */
class useractionlogger_ActionBase extends f_action_BaseAction
{

	/**
	 * Returns the useractionlogger_ActiondefService to handle documents of type "modules_useractionlogger/actiondef".
	 *
	 * @return useractionlogger_ActiondefService
	 */
	public function getActiondefService()
	{
		return useractionlogger_ActiondefService::getInstance();
	}
}