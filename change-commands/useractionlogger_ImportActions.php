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

		$ls = LocaleService::getInstance();
		if ( f_util_ArrayUtils::isEmpty($params))
		{
			$params = array();
			foreach (glob("modules/*/setup/useractionlogger.xml") as $path) 
			{
				$module = basename(dirname(dirname($path)));
				$params[] = $module;
			}
		}

		foreach ($params as $module)
		{
			$packageName = "modules_".$module;
			$path = FileResolver::getInstance()->setPackageName($packageName)->setDirectory('setup')->getPath('useractionlogger.xml');
			if ($path !== null)
			{
				$this->log("Import user action log for package : $module");
				$scriptReader = import_ScriptReader::getInstance();
				$scriptReader->execute($path);
			}
		}
		
		$this->getParent()->executeCommand('clear-webapp-cache');
		return $this->quitOk("Actions successfully imported");
	}
}