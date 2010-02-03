<?php
/**
 * useractionlogger_patch_0300
 * @package modules.useractionlogger
 */
class useractionlogger_patch_0300 extends patch_BasePatch
{
	//  by default, isCodePatch() returns false.
	//  decomment the following if your patch modify code instead of the database structure or content.
	/**
	 * Returns true if the patch modify code that is versionned.
	 * If your patch modify code that is versionned AND database structure or content,
	 * you must split it into two different patches.
	 * @return Boolean true if the patch modify code that is versionned.
	 */
	//	public function isCodePatch()
	//	{
	//		return true;
	//	}
	

	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		parent::execute();
		$ms = ModuleService::getInstance();
		$packages = $ms->getModules();
		foreach ($packages as $packageName)
		{
			$path = FileResolver::getInstance()->setPackageName($packageName)->setDirectory('setup')->getPath('useractionlogger.xml');
			if ($path !== null)
			{
				$moduleName = $ms->getShortModuleName($packageName);
				$this->log("Migrate user action log for module : $moduleName");
				$localFilePath = f_util_FileUtils::buildWebeditPath('modules', $moduleName, 'locale', 'bo', 'useractionlogger.xml');
				if (! file_exists($localFilePath))
				{
					f_util_FileUtils::writeAndCreateContainer($localFilePath, '<?xml version="1.0" encoding="utf-8"?><localization></localization>');
				}
				
				$this->migrateActionLogger($moduleName, $path, $localFilePath);
			}
		}
	}
	
	private function migrateActionLogger($moduleName, $path, $localFilePath)
	{
		$oldLocalPath = f_util_FileUtils::buildWebeditPath('modules', $moduleName, 'locale', 'bo', 'actions.xml');
		$oldLocalDoc = new DOMDocument('1.0', 'UTF-8');
		if (file_exists($oldLocalPath))
		{
			$oldLocalDoc->preserveWhiteSpace = false;
			$oldLocalDoc->formatOutput = true;
			$oldLocalDoc->load($oldLocalPath);
			$oldModified = false;
		}
		$xpath = new DOMXPath($oldLocalDoc);
		
		$localDoc = new DOMDocument('1.0', 'UTF-8');
		$localDoc->preserveWhiteSpace = false;
		$localDoc->formatOutput = true;
		
		$localDoc->load($localFilePath);
		
		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->load($path);
		$modified = false;
		foreach ($doc->getElementsByTagName('actiondef') as $actiondef)
		{
			$actionName = $actiondef->getAttribute('actionname');
			$localKey = $this->buildLocalKey($moduleName, $actionName);
			if ($actiondef->getAttribute('label') != $localKey)
			{
				$modified = true;
				$local = $actiondef->getAttribute('label');
				$actiondef->setAttribute('label', $localKey);
				
				$id = str_replace('.', '-', $actionName);
				$localdef = $localDoc->documentElement->appendChild($localDoc->createElement('entity'));
				$localdef->setAttribute('id', $id);
				$frloc = $localdef->appendChild($localDoc->createElement('locale'));
				$frloc->setAttribute('lang', 'fr');
				$frloc->appendChild($localDoc->createTextNode($local));
				
				$id = 'log-' . $id;
				$oldNodesLocal = $xpath->query('//entity[@id="' . $id . '"]');
				if ($oldNodesLocal->length === 1)
				{
					$oldModified = true;
					$oldNodeLocal = $oldNodesLocal->item(0);
					$oldNodeLocal->parentNode->removeChild($oldNodeLocal);
					$localDoc->documentElement->appendChild($localDoc->importNode($oldNodeLocal, true));
				}
				else
				{
					$localdef = $localDoc->documentElement->appendChild($localDoc->createElement('entity'));
					$localdef->setAttribute('id', $id);
					$frloc = $localdef->appendChild($localDoc->createElement('locale'));
					$frloc->setAttribute('lang', 'fr');
					$frloc->appendChild($localDoc->createTextNode($id));
				}
			}
		}
		
		if ($modified)
		{
			if ($oldModified)
			{
				echo "Save $oldLocalPath\n";
				$oldLocalDoc->save($oldLocalPath);
			}
			
			echo "Save $localFilePath\n";
			$localDoc->save($localFilePath);
			
			echo "Save $path\n";
			$doc->save($path);
		}
	}
	
	private function buildLocalKey($moduleName, $actionName)
	{
		return 'modules.' . $moduleName . '.bo.useractionlogger.' . str_replace('.', '-', $actionName);
	}
	
	private function buildLocalFilterKey($moduleName, $actionName)
	{
		return 'modules.' . $moduleName . '.bo.useractionlogger.log-' . str_replace('.', '-', $actionName);
	}
	/**
	 * Returns the name of the module the patch belongs to.
	 *
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'useractionlogger';
	}
	
	/**
	 * Returns the number of the current patch.
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0300';
	}
}