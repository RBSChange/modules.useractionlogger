<?php
/**
 * @package modules.useractionlogger.setup
 */
class useractionlogger_Setup extends object_InitDataSetup
{
	public function install()
	{
		$this->executeModuleScript('init.xml');
		$packages = ModuleService::getInstance()->getModules();
		foreach ($packages as $packageName) 
		{
			$path = FileResolver::getInstance()->setPackageName($packageName)->setDirectory('setup')->getPath('useractionlogger.xml');
			if ($path !== null)
			{
				$this->log("Import user action log for package : $packageName");
				$scriptReader = import_ScriptReader::getInstance();
				$scriptReader->execute($path);
			}
		}
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