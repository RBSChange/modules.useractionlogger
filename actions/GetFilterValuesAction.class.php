<?php
/**
 * useractionlogger_GetFilterValuesAction
 * @package modules.useractionlogger.actions
 */
class useractionlogger_GetFilterValuesAction extends useractionlogger_Action
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$filterName = $request->getParameter('filtername');
		switch ($filterName) 
		{
			case 'module':
				$result = $this->buildModuleList();
				break;
			case 'action':
				$result = $this->buildActionList();
				break;
			default:
				$result = $this->buildUserList();
				break;
		}

		echo JsonService::getInstance()->encode($result);
		return View::NONE;
	}
	
	private function buildUserList()
	{
		$service = useractionlogger_ModuleService::getInstance();
		$userIds = $service->getUserInLog();
		$result = array();
		foreach ($userIds as $userId) 
		{
			try 
			{
				$user = DocumentHelper::getDocumentInstance($userId);
				if ($user instanceof users_persistentdocument_user)
				{
					$result[] = array('id' => $userId, 'label' => $user->getFullname());
				}
				else
				{
					$result[] = array('id' => $userId, 'label' => 'Deleted #'.$userId);
				}
			}
			catch (Exception $e)
			{
				$result[] = array('id' => $userId, 'label' => 'Deleted #'.$userId);
			}
		}
		return $result;
	}
	
	private function buildModuleList()
	{
		$service = useractionlogger_ModuleService::getInstance();
		$moduleNames = $service->getModuleInLog();
		
		$moduleList = array();
		foreach ($moduleNames as $moduleName)
		{		
			$moduleLocalizedName = f_Locale::translate('&modules.' . $moduleName . '.bo.general.Module-name;');
			$moduleList[$moduleLocalizedName] = array('id' => $moduleName, 'label' => $moduleLocalizedName);
		}
		ksort($moduleList);
		return array_values($moduleList);;
	}
	
	private function buildActionList()
	{
		$allActions = useractionlogger_ActiondefService::getInstance()->getActionList();
		
		$service = useractionlogger_ModuleService::getInstance();
		$actionNames = $service->getActionInLog();
		
		$actionList = array();
		foreach ($actionNames as $actionName)
		{	
			$moduleName = $allActions[$actionName];
			$localKey =	'&modules.' . substr($moduleName, 0, strpos($moduleName, ';')) . '.bo.useractionlogger.Log-' . str_replace('.', '-', $actionName) . ';';
			$actionLabel = f_Locale::translate($localKey);
			$actionList[$actionLabel] = array('id' => $actionName, 'label' => $actionLabel, 'module' => $moduleName);
		}
		ksort($actionList);
		return array_values($actionList);
	}
}