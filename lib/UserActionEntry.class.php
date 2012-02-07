<?php
class UserActionEntry
{
	private $id;
	private $dateTime;
	private $userId;
	private $documentId;
	private $moduleName;
	private $actionName;
	private $info;
	private $documentLinkId;

	public function __construct($dataRow)
	{
		$this->id = intval($dataRow['entry_id']);
		$this->dateTime = $dataRow['entry_date'];
		$this->userId = intval($dataRow['user_id']);
		$this->documentId = intval($dataRow['document_id']);
		$this->moduleName = $dataRow['module_name'];
		$this->actionName = $dataRow['action_name'];
		$this->info = unserialize($dataRow['info']);
		$this->info['logdescription'] = LocaleService::getInstance()->transBO('m.' . $this->moduleName. '.bo.useractionlogger.' .str_replace('.', '-',$this->actionName), array('ucf'), $this->info);
		$this->documentLinkId = intval($dataRow['link_id']);
	}

	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->info['logdescription'];
	}

	/**
	 * @return integer
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $this->info['username'];
	}

	/**
	 * @return string
	 */
	public function getDateTime()
	{
		return $this->dateTime;
	}

	/**
	 * @return String
	 */
	public function getName()
	{
		return $this->actionName;
	}

	/**
	 * @return string
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}

	/**
	 * @return integer
	 */
	public function getDocumentId()
	{
		return $this->documentId;
	}

	/**
	 * @return string
	 */
	public function getDocumentLabel()
	{
		if (isset($this->info['documentlabel']))
		{
			return $this->info['documentlabel'];
		}
		return null;
	}

	/**
	 * @return boolean
	 */
	public function hasLinkedDocument()
	{
		return $this->documentLinkId > 0;
	}
}