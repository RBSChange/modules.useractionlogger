<?php
/**
 * useractionlogger_GetUserActionListAction
 * @package modules.useractionlogger.actions
 */
class useractionlogger_GetUserActionListAction extends useractionlogger_Action
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$moduleFilter = $request->getParameter('modulefilter');
		$actionFilter = $request->getParameter('actionfilter');
		$userFilter = $request->getParameter('userfilter');
		$sortOnField = $request->getParameter('sortonfield', 'date');
		$sortDirection = $request->getParameter('sortdirection', 'DESC');
		$pageSize = intval($request->getParameter('pagesize', 100));
		$pageIndex = intval($request->getParameter('pageindex', 0));
		$startIndex = $pageIndex * $pageSize;
		
		$result = array();
		$nbresult = useractionlogger_ModuleService::getInstance()->getCountUserActionEntry($userFilter, $moduleFilter, $actionFilter, null);
		if ($startIndex >= $nbresult) 
		{
			$startIndex = 0;
			$pageIndex = 0;
		}
		$result['header'] = array('count' => $nbresult, 'pageindex' => $pageIndex);
		$result['entries'] = array();
		
		$userActionEntries = useractionlogger_ModuleService::getInstance()->getUserActionEntry($userFilter, $moduleFilter, $actionFilter, null, $startIndex , $pageSize, $sortOnField, $sortDirection);
		foreach ($userActionEntries as $uAE) 
		{
			$dateFormated = date_Formatter::toDefaultDateTimeBO(date_Converter::convertDateToLocal($uAE->getDateTime()));
			$result['entries'][] = array($dateFormated, $uAE->getUserName(), $uAE->getLabel(), $uAE->hasLinkedDocument(), $uAE->getDocumentId());	
		}
		
		$result['header']['resultcount'] = count($result['entries']);
		echo JsonService::getInstance()->encode(array($result));
		return View::NONE;
	}
}