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
class ContenthistoryModelHistory extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'version_id', 'h.version_id',
				'version_note', 'h.version_note',
				'save_date', 'h.save_date',
				'editor_user_id', 'h.editor_user_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');
		$input = JFactory::getApplication()->input;
		$itemId = $input->get('item_id', 0, 'integer');
		$typeId = $input->get('type_id', 0, 'integer');
		$typeAlias = $input->get('type_alias', '', 'string');

		$this->setState('item_id', $itemId);
		$this->setState('type_id', $typeId);
		$this->setState('type_alias', $typeAlias);
		// Load the parameters.
		$params = JComponentHelper::getParams('com_contenthistory');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('h.save_date', 'DESC');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'h.version_id, h.ucm_item_id, h.ucm_type_id, h.version_note, h.save_date, h.editor_user_id,' .
					'h.character_count, h.sha1_hash, h.version_data'
			)
		);
		$query->from($db->quoteName('#__ucm_history') . ' AS h');
		$query->where($db->quoteName('h.ucm_item_id') . ' = ' . $this->getState('item_id'));
		$query->where($db->quoteName('h.ucm_type_id') . ' = ' . $this->getState('type_id'));

		// Join over the users for the editor
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id = h.editor_user_id');

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->quoteName($orderCol) . $orderDirn);
		return $query;
	}
}
