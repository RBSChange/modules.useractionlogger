<?php
class useractionlogger_PurgeLogTask extends task_SimpleSystemTask  
{
	/**
	 * @see task_SimpleSystemTask::execute()
	 *
	 */
	protected function execute()
	{		
		$maxDaysHistory = intval(Framework::getConfigurationValue('modules/useractionlogger/maxDaysHistory', 60));
		if ($maxDaysHistory > 0)
		{
			$minPurgeDate = date_Calendar::getInstance()->sub(date_Calendar::DAY, $maxDaysHistory)->toString();
			$tm = f_persistentdocument_TransactionManager::getInstance();
			try
			{
				$tm->beginTransaction();
				
				$result = $tm->getPersistentProvider()->deleteUserActionEntries($minPurgeDate);
				if (Framework::isInfoEnabled())
				{
					Framework::info('Deleted Users Actions log Entries before ' . $minPurgeDate . ' ('.$maxDaysHistory.' days): ' . $result);
				}
				$tm->commit();
			} 
			catch (Exception $e) 
			{
				$tm->rollback($e);
				throw $e;
			}
		}
		else
		{
			if (Framework::isInfoEnabled())
			{
				Framework::info('Unlimited Users Actions log Entries');
			}
		}
	}
}
