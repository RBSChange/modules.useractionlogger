<?php
/**
 * useractionlogger_GetFilterValuesAction
 * @package modules.useractionlogger.actions
 */
class useractionlogger_GetFilterValuesAction extends change_Action
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
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
		return change_View::NONE;
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
		$modifier = array('ucf');
		$ls = LocaleService::getInstance();
		foreach ($moduleNames as $moduleName)
		{		
			$moduleLocalizedName = $ls->transBO('m.' . $moduleName . '.bo.general.module-name', $modifier);
			$moduleList[$moduleLocalizedName] = array('id' => $moduleName, 'label' => $moduleLocalizedName);
		}
		ksort($moduleList);
		return array_values($moduleList);;
	}
	
	private function buildActionList()
	{
		$allActions = useractionlogger_ActiondefService::getInstance()->getActionDefIdForName();
		
		$service = useractionlogger_ModuleService::getInstance();
		$actionNames = $service->getActionInLog();
		
		$actionList = array();
		$modifier = array('ucf');
		$ls = LocaleService::getInstance();
		foreach ($actionNames as $actionName)
		{	
			$datas = $allActions[$actionName];
			$moduleName = $datas['modulename'];
			if (strpos($datas['label'], '.bo.useractionlogger.'))
			{
				$localKey =	str_replace('.bo.useractionlogger.', '.bo.useractionlogger.log-', $datas['label']);
			}
			else
			{
				$localKey =	'm.' . $moduleName . '.bo.useractionlogger.log-' . str_replace('.', '-', $actionName);
			}
			$actionLabel = $ls->transBO($localKey, $modifier);
			$actionList[$actionLabel] = array('id' => $actionName, 'label' => $actionLabel, 'module' => $moduleName);
		}
		ksort($actionList);
		return array_values($actionList);
	}
}