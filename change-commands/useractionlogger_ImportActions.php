<?php
/**
 * commands_useractionlogger_ImportActions
 * @package modules.useractionlogger.command
 */
class commands_useractionlogger_ImportActions extends commands_AbstractChangeCommand
{
	/**
	 * @return String
	 */
	function getUsage()
	{
		return "[module1 module2 ... module3]";
	}

	/**
	 * @return String
	 */
	function getDescription()
	{
		return "Import user actions";
	}
	
	/**
	 * @param Integer $completeParamCount the parameters that are already complete in the command line
	 * @param String[] $params
	 * @param array<String, String> $options where the option array key is the option name, the potential option value or true
	 * @return String[] or null
	 */
	function getParameters($completeParamCount, $params, $options, $current)
	{
		$components = array();
		foreach (glob("modules/*/setup/useractionlogger.xml") as $path) 
		{
			$module = basename(dirname(dirname($path)));
			$components[] = $module;
		}
		return array_diff($components, $params);
	}
	
	/**
	 * @param String[] $params
	 * @param array<String, String> $options where the option array key is the option name, the potential option value or true
	 * @see c_ChangescriptCommand::parseArgs($args)
	 */
	function _execute($params, $options)
	{
		$this->message("== Import Actions ==");
		$this->loadFramework();

		if (f_util_ArrayUtils::isEmpty($params))
		{
			$params = useractionlogger_ModuleService::getInstance()->getModulesWithUserAction();
		}

		foreach ($params as $module)
		{
			if (useractionlogger_ModuleService::getInstance()->importInitScript($module))
			{
				$this->log("User action log imported for package : $module");
			}
		}
		
		$this->getParent()->executeCommand('clear-webapp-cache');
		return $this->quitOk("Actions successfully imported");
	}
}