<?php
/**
 * useractionlogger_ActiondefScriptDocumentElement
 * @package modules.useractionlogger.persistentdocument.import
 */
class useractionlogger_ActiondefScriptDocumentElement extends import_ScriptDocumentElement
{
	/**
	 * @return useractionlogger_persistentdocument_actiondef
	 */
	protected function initPersistentDocument()
	{
		$moduleName = $this->attributes['modulename'];
		$actionName = $this->attributes['actionname'];
		$service = useractionlogger_ActiondefService::getInstance();
		$inst = $service->getByModuleAndCode($moduleName, $actionName);
		if ($inst === null)
		{
			$inst = $service->getNewDocumentInstance();
		}
		return $inst;
	}
}