<?php
/**
 * useractionlogger_GetFilterValuesAction
 * @package modules.useractionlogger.actions
 */
class useractionlogger_GetFilterValuesAction extends f_action_BaseAction
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
		$pp = f_persistentdocument_PersistentProvider::getInstance();
		$userModel = f_persistentdocument_PersistentDocumentModel::getInstance('users', 'user');
		$validModelNames = array_merge(array('modules_users/user'), $userModel->getChildrenNames());
		$result = array();
		foreach (useractionlogger_ModuleService::getInstance()->getUserInLog() as $userId) 
		{
			$modelName = $pp->getDocumentModelName($userId);
			if (in_array($modelName, $validModelNames))
			{
				$user = users_persistentdocument_user::getInstanceById($userId);
				$label = $user->getFullname() . ' #' . $userId;
				$result[f_util_StringUtils::strtolower($label)] = array('id' => $userId, 'label' => $label);
			}
			elseif ($userId == -1)
			{
				$result['__system'] = array('id' => $userId, 'label' => 'System');
			}
			else 
			{
				$result['zzz_' . $userId] = array('id' => $userId, 'label' => 'Deleted #' . $userId);
			}
		}
		ksort($result);
		return array_values($result);
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