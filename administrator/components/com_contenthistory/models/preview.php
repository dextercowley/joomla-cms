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
 * @since       1.6
 */
class ContenthistoryModelPreview extends JModelItem
{

	/**
	 * Method to get a version history row.
	 *
	 * @param  integer  $versionId  The id of the JTableContenthistory table.
	 *
	 * @return mixed    On success, standard object with row data. False on failure.
	 *
	 * @since   3.2
	 */
	public function getItem()
	{
		$table = JTable::getInstance('Contenthistory');
		$helper = new JHelperContenthistory(null);
		$versionId = JFactory::getApplication()->input->getInt('version_id');
		if ($table->load($versionId))
		{
			$object = json_decode($table->version_data);
			$object = $helper->decodeFields($object);
			$result = new stdClass();
			$result->save_date = $table->save_date;
			$result->version_note = $table->version_note;
			$result->data = $object;
		}
		else
		{
			$result = false;
		}
		return $result;
	}

}
