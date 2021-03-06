<?php
/**
 * @package modules.useractionlogger.lib.services
 */
class useractionlogger_ModuleService extends ModuleBaseService
{
	/**
	 * Singleton
	 * @var useractionlogger_ModuleService
	 */
	private static $instance = null;

	/**
	 * @return useractionlogger_ModuleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
				
	/**
	 * @param users_persistentdocument_user
	 * @param String $actionName
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param array $info
	 * @param String $moduleName
	 */
	public function addUserDocumentEntry($user, $actionName, $document, $info, $moduleName)
	{
		$mask = $this->getActionLabelMask($moduleName, $actionName);
		if ($mask === null && !Framework::inDevelopmentMode()) 
		{
			return null;
		}
	
		if ($user instanceof users_persistentdocument_user)
		{
			$userId = $user->getId();
			$userFullname = $user->getFullname();
		}
		else 
		{
			$userId = -1;
			$userFullname = 'System';
		}
		
		if (!is_array($info)) {$info = array();}
		$info['username'] = $userFullname;
		
		if ($document !== null)
		{
			$documentId = $document->getId();
			if (!isset($info['lang']) && $document->isLocalized())
			{
				$info['lang'] = RequestContext::getInstance()->getLang();
			}
			
			if ($document->getPersistentModel()->useCorrection())
			{
				$correctionofId = (isset($info['correctionofid'])) ? $info['correctionofid'] : $document->getCorrectionofid();
				if (intval($correctionofId) > 0) 
				{
					$info['correctionid'] = $documentId;
					$documentId = $correctionofId;	
				}	 	
			}
			
			if (!isset($info['documentlabel'])) 
			{
				$info['documentlabel'] = $document->getTreeNodeLabel();
			}
			$document->getDocumentService()->addActionLogInfo($document, $actionName, $info);
		}
		else
		{
			$documentId = null;
		}
		
		//In DevelopmentMode generate default ActionDef
		if ($mask === null)
		{
			$mask = $this->generateDefaultActionDef($actionName, $info, $moduleName);
			if ($mask === null) {return null;}
		}
		
		$tm = $this->getTransactionManager();
		try 
		{
			$tm->beginTransaction();
			$date_entry = date_Calendar::now()->toString();
			if (!isset($info['logdescription']))
			{
				$info['logdescription'] = $mask;
			}
			$serializedInfo = serialize($info);
			$entryId = $this->getPersistentProvider()->addUserActionEntry($date_entry, $userId, $moduleName, $actionName, $documentId, $info['username'], $serializedInfo);
			if (Framework::isInfoEnabled())
			{
				Framework::info(__METHOD__ . "($actionName, $moduleName) => $entryId");
			}
			$tm->commit();
		}
		catch (Exception $e)
		{
			$tm->rollBack($e);
			return null;
		}
		return $entryId;
	}
	
	/**
	 * @param String $actionName
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param array $info
	 * @param String $moduleName
	 */
	public function addCurrentUserDocumentEntry($actionName, $document, $info, $moduleName)
	{
		$user = users_UserService::getInstance()->getCurrentUser();
		if ($user === null)
		{
			return null;
		}
		else
		{
			return $this->addUserDocumentEntry($user, $actionName, $document, $info, $moduleName);
		}
	}
	
	/**
	 * @param String $moduleName
	 * @param String $codeName
	 * @return String | null
	 */
	private function getActionLabelMask($moduleName, $codeName)
	{
		$actionDef = useractionlogger_ActiondefService::getInstance()->getByModuleAndCode($moduleName, $codeName);
		if ($actionDef !== null)
		{
			return $actionDef->getLabel();
		}
		return null;
	}
	
	/**
	 * @param String $actionName
	 * @param array $info
	 * @param String $moduleName
	 * @return String | null
	 */
	private function generateDefaultActionDef($actionName, $info, $moduleName)
	{
		$defFilePath = f_util_FileUtils::buildWebeditPath('modules', $moduleName, 'setup', 'useractionlogger.xml');
		if (is_writable(dirname($defFilePath)))
		{
			Framework::info(__METHOD__ . "($actionName, $moduleName, $defFilePath)");
			
			$defdoc = new DOMDocument('1.0', 'UTF-8');
			$defdoc->preserveWhiteSpace = false;
			$defdoc->formatOutput = true;
						
			if (file_exists($defFilePath))
			{
				$defdoc->load($defFilePath);
			}
			else
			{
				$defdoc->loadXML('<?xml version="1.0" encoding="UTF-8"?>
<script>
	<binding fileName="modules/useractionlogger/persistentdocument/import/useractionlogger_binding.xml" />
</script>');
			}
			$defdoc->formatOutput = true; 
			
			$actiondef = $defdoc->createElement('actiondef');
			$actiondef->setAttribute('modulename', $moduleName);
			$actiondef->setAttribute('actionname', $actionName);
			$localkey = 'm.' . $moduleName . '.bo.useractionlogger.' . str_replace('.', '-', $actionName);
			
			$label = $actionName;
			foreach (array_keys($info) as $key) 
			{
				if ($key === 'correctionid' || $key === 'username') {continue;}
				$label .= ' {'.$key.'}';
			}
			$actiondef->setAttribute('label', $localkey);
			$defdoc->documentElement->appendChild($actiondef);	
			$defdoc->save($defFilePath);
			
			$ls = LocaleService::getInstance();
			$baseKey = 'm.' . $moduleName . '.bo.useractionlogger';
			$lcid = $ls->getLCID('fr');
			$id = strtolower(str_replace('.', '-', $actionName));
			
			$keysInfos = array();
			$keysInfos[$lcid][$id]  = $label;
			
			$id = 'log-'.$id;
			$keysInfos[$lcid][$id] = str_replace('-', ' ', $id);
			$ls->updatePackage($baseKey, $keysInfos, false, true);
			
			$persistDoc = useractionlogger_ActiondefService::getInstance()->getNewDocumentInstance();
			$persistDoc->setLabel($localkey);
			$persistDoc->setModulename($moduleName);
			$persistDoc->setActionname($actionName);
			$persistDoc->save();
			return $localkey;
		}
		else
		{
			Framework::info(__METHOD__ . " - READONLY " . dirname($defFilePath));
		}
		return null;
	}
		
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
	 * @return UserActionEntry[]
	 */
	public final function getUserActionEntry($userId, $moduleName, $actionName, $documentId, $rowIndex, $rowCount, $sortOnField, $sortDirection)
	{
		$result = array();
		$data = $this->getPersistentProvider()->getUserActionEntry($userId, $moduleName, $actionName, $documentId, $rowIndex, $rowCount, $sortOnField, $sortDirection);
		foreach ($data as $dataRow) 
		{
			$result[] = new UserActionEntry($dataRow);
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
		foreach (glob(WEBEDIT_HOME . "/modules/*/setup/useractionlogger.xml") as $path) 
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
