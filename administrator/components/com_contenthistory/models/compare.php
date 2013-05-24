<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contenthistory
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Methods supporting a list of contenthistory records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contenthistory
 * @since       3.2
 */
class ContenthistoryModelCompare extends JModelItem
{

	/**
	 * Method to get a version history row.
	 *
	 * @param  integer  $versionId  The id of the JTableContenthistory table.
	 *
	 * @return mixed    On success, array of populated tables. False on failure.
	 *
	 * @since   3.2
	 */
	public function getItems()
	{
		$table1 = JTable::getInstance('Contenthistory');
		$table2 = JTable::getInstance('Contenthistory');
		$id1 = JFactory::getApplication()->input->getInt('id1');
		$id2 = JFactory::getApplication()->input->getInt('id2');
		$helper = new JHelperContenthistory(null);
		$result = array();
		if ($table1->load($id1) && $table2->load($id2))
		{
			foreach (array($table1, $table2) as $table)
			{
				$object = new stdClass();
				$object->data = $helper->decodeFields(json_decode($table->version_data));
				$object->version_note = $table->version_note;
				$object->save_date = $table->save_date;
				$result[] = $object;
			}
			return $result;
		}
		else
		{
			return false;
		}
	}

}
