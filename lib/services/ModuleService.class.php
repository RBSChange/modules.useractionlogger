<?php
/**
 * @package modules.useractionlogger.lib.services
 * @method useractionlogger_ModuleService getInstance()
 */
class useractionlogger_ModuleService extends ModuleBaseService
{
	/**
	 * @param Integer $userId
	 * @param String $moduleName
	 * @param String $actionName
	 * @param Integer $documentId
	 * @return Integer
	 */
	public function getCountUserActionEntry($userId, $moduleName, $actionName, $documentId)
	{
		return $this->getPersistentProvider()->getCountUserActionEntry($userId, $moduleName, $actionName, $documentId);
	}

	/**
	 * @param Integer $userId
	 * @param String $moduleName
	 * @param String $actionName
	 * @param Integer $documentId
	 * @param Integer $rowIndex
	 * @param Integer $rowCount
	 * @param String $sortOnField (date | user)
	 * @param String $sortDirection (ASC | DESC)
	 * @return useractionlogger_Entry[]
	 */
	public final function getUserActionEntry($userId, $moduleName, $actionName, $documentId, $rowIndex, $rowCount, $sortOnField, $sortDirection)
	{
		$result = array();
		$data = $this->getPersistentProvider()->getUserActionEntry($userId, $moduleName, $actionName, $documentId, $rowIndex, $rowCount, $sortOnField, $sortDirection);
		foreach ($data as $dataRow)
		{
			$result[] = new useractionlogger_Entry($dataRow);
		}
		return $result;
	}
	
	/**
	 * @return String[]
	 */
	public final function getActionInLog()
	{
		$result = array();
		$data = $this->getPersistentProvider()->getDistinctLogEntry('action');
		foreach ($data as $dataRow)
		{
			$result[] = $dataRow['distinctvalue'];
		}
		return $result;
	}
	
	/**
	 * @return String[]
	 */
	public final function getModuleInLog()
	{
		$result = array();
		$data = $this->getPersistentProvider()->getDistinctLogEntry('module');
		foreach ($data as $dataRow)
		{
			$result[] = $dataRow['distinctvalue'];
		}
		return $result;
	}
		
	/**
	 * @return Integer[]
	 */
	public final function getUserInLog()
	{
		$result = array();
		$data = $this->getPersistentProvider()->getDistinctLogEntry('user');
		foreach ($data as $dataRow)
		{
			$result[] = $dataRow['distinctvalue'];
		}
		return $result;
	}
	
	/**
	 * @param string $module
	 * @return boolean
	 */
	public function importInitScript($module)
	{
		$packageName = "modules_".$module;
		$path = FileResolver::getInstance()->setPackageName($packageName)
			->setDirectory('setup')->getPath('useractionlogger.xml');
		if ($path !== null)
		{
			$scriptReader = import_ScriptReader::getInstance();
			$scriptReader->execute($path);
			return true;
		}
		return false;
	}
	
	/**
	 * @return string[]
	 */
	public function getModulesWithUserAction()
	{
		$params = array();
		foreach (glob(PROJECT_HOME . "/modules/*/setup/useractionlogger.xml") as $path) 
		{
			$module = basename(dirname(dirname($path)));
			$params[] = $module;
		}
		return $params; 
	}
	
	
	/**
	 * @return void
	 */
	public function importAllInitScript()
	{
		foreach ($this->getModulesWithUserAction() as $module)
		{
			$this->importInitScript($module);
		}
	}
	
	/**
	 * @return void
	 */
	public function addPurgeLogTask()
	{
		$tasks = task_PlannedtaskService::getInstance()->getBySystemtaskclassname('useractionlogger_PurgeLogTask');
		if (count($tasks) == 0)
		{
			$task = task_PlannedtaskService::getInstance()->getNewDocumentInstance();
			$task->setSystemtaskclassname('useractionlogger_PurgeLogTask');
			$task->setLabel('useractionlogger_PurgeLogTask');
			$task->setMinute(-1);
			$task->setHour(-1);
			$task->setMaxduration(2);
				
			$task->save(ModuleService::getInstance()->getSystemFolderId('task', 'useractionlogger'));
		}
	}
}