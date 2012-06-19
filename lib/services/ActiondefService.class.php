<?php
/**
 * @package modules.useractionlogger
 * @method useractionlogger_ActiondefService getInstance()
 */
class useractionlogger_ActiondefService extends f_persistentdocument_DocumentService
{
	/**
	 * @return useractionlogger_persistentdocument_actiondef
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_useractionlogger/actiondef');
	}

	/**
	 * Create a query based on 'modules_useractionlogger/actiondef' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_useractionlogger/actiondef');
	}
	
	/**
	 * @param string $moduleName
	 * @param string $codeName
	 * @return useractionlogger_persistentdocument_actiondef
	 */
	public function getByModuleAndCode($moduleName, $codeName)
	{
		return $this->createQuery()
			->add(Restrictions::eq('modulename', $moduleName))
			->add(Restrictions::eq('actionname', $codeName))
			->findUnique();
	}
	
	/**
	 * @return string[]
	 */
	public function getModuleList()
	{
		$rows = $this->createQuery()->setProjection(Projections::groupProperty('modulename', 'modulename'))->find();
		$modules = array();
		foreach ($rows as $row) 
		{
			$modules[] = $row['modulename'];
		}
		return $modules;
	}	
	
	/**
	 * @return string[]
	 */
	public function getActionList()
	{
		$rows = $this->createQuery()->setProjection(Projections::groupProperty('actionname', 'actionname'), Projections::groupProperty('modulename', 'modulename'))->find();
		$actions = array();
		foreach ($rows as $row) 
		{
			if (isset($actions[$row['actionname']]))
			{
				$actions[$row['actionname']] .= $row['modulename'] . ';';
			}
			else
			{
				$actions[$row['actionname']] = $row['modulename'] . ';';
			}
		}
		return $actions;		
	}
	
	/**
	 * @return string[]
	 */
	public function getActionDefIdForName()
	{
		$rows = $this->createQuery()->setProjection(Projections::groupProperty('modulename', 'modulename'), 
					Projections::groupProperty('actionname', 'actionname'),
					Projections::groupProperty('label', 'label'))->find();
		$actions = array();
		foreach ($rows as $row) 
		{
			$actions[$row['actionname']] = $row;
		}
		return $actions;		
	}
}