<?php
/**
 * useractionlogger_ActiondefService
 * @package useractionlogger
 */
class useractionlogger_ActiondefService extends f_persistentdocument_DocumentService
{
	/**
	 * @var useractionlogger_ActiondefService
	 */
	private static $instance;

	/**
	 * @return useractionlogger_ActiondefService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

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
		return $this->pp->createQuery('modules_useractionlogger/actiondef');
	}
	
	/**
	 * @param String $moduleName
	 * @param String $codeName
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
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
//	protected function preSave($document, $parentNodeId = null)
//	{
//
//	}


	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preInsert($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postSave($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @return void
	 */
//	protected function preDelete($document)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @return void
	 */
//	protected function postDelete($document)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @return boolean true if the document is publishable, false if it is not.
	 */
//	public function isPublishable($document)
//	{
//		$result = parent::isPublishable($document);
//		return $result;
//	}


	/**
	 * Methode à surcharger pour effectuer des post traitement apres le changement de status du document
	 * utiliser $document->getPublicationstatus() pour retrouver le nouveau status du document.
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @param String $oldPublicationStatus
	 * @param array<"cause" => String, "modifiedPropertyNames" => array, "oldPropertyValues" => array> $params
	 * @return void
	 */
//	protected function publicationStatusChanged($document, $oldPublicationStatus, $params)
//	{
//	}

	/**
	 * Correction document is available via $args['correction'].
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Array<String=>mixed> $args
	 */
//	protected function onCorrectionActivated($document, $args)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $fromDocument
	 * @param f_persistentdocument_PersistentDocument $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedFrom($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * @param f_persistentdocument_PersistentDocument $fromDocument
	 * @param useractionlogger_persistentdocument_actiondef $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedTo($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * Called before the moveToOperation starts. The method is executed INSIDE a
	 * transaction.
	 *
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Integer $destId
	 */
//	protected function onMoveToStart($document, $destId)
//	{
//	}

	/**
	 * @param useractionlogger_persistentdocument_actiondef $document
	 * @param Integer $destId
	 * @return void
	 */
//	protected function onDocumentMoved($document, $destId)
//	{
//	}

	/**
	 * this method is call before save the duplicate document.
	 * If this method not override in the document service, the document isn't duplicable.
	 * An IllegalOperationException is so launched.
	 *
	 * @param f_persistentdocument_PersistentDocument $newDocument
	 * @param f_persistentdocument_PersistentDocument $originalDocument
	 * @param Integer $parentNodeId
	 *
	 * @throws IllegalOperationException
	 */
//	protected function preDuplicate($newDocument, $originalDocument, $parentNodeId)
//	{
//		throw new IllegalOperationException('This document cannot be duplicated.');
//	}
}