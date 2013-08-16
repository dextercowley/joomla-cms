<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Table class supporting modified pre-order tree traversal behavior.
 *
 * @package     Joomla
 * @subpackage  Table
 * @link        http://docs.joomla.org/JTableObserver
 * @since       3.1.2
 */
class JTableObserverContenthistory extends JTableObserver
{
	/**
	 * Helper object for storing and deleting version history information associated with this table observer
	 *
	 * @var  JHelperContenthistory
	 */
	protected $contenthistoryHelper;

	/**
	 * The pattern for this table's TypeAlias
	 * @var  string
	 */
	protected $typeAliasPattern = null;

	/**
	 * Not public, so marking private and deprecated, but needed internally in parseTypeAlias for
	 * PHP < 5.4.0 as it's not passing context $this to closure function.
	 *
	 * @var JTableObserverContenthistory
	 * @deprecated Never use this
	 * @private
	 */
	public static $_myTableForPregreplaceOnly;

	/**
	 * Creates the associated observer instance and attaches it to the $observableObject
	 * Creates the associated content history helper class instance
	 * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
	 *
	 * @param   JObservableInterface|JTable  $observableObject  The subject object to be observed
	 * @param   array                        $params            ( 'typeAlias' => $typeAlias )
	 *
	 * @return  JObserverInterface|JTableObserverContenthistory
	 */
	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$typeAlias = $params['typeAlias'];

		$observer = new self($observableObject);

		$observer->contenthistoryHelper = new JHelperContenthistory($typeAlias);
		$observer->typeAliasPattern = $typeAlias;

		return $observer;
	}

	/**
	 * Post-processor for $table->store($updateNulls)
	 *
	 * @param   boolean  &$result  The result of the load
	 *
	 * @return  void
	 */
	public function onAfterStore(&$result)
	{
		if ($result)
		{
			$aliasParts = explode('.', $this->contenthistoryHelper->typeAlias);
			if (JComponentHelper::getParams($aliasParts[0])->get('save_history', 0))
			{
				$this->contenthistoryHelper->store($this->table);
			}
		}
	}

	/**
	 * Pre-processor for $table->delete($pk)
	 *
	 * @param   mixed   $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  void
	 *
	 * @throws  UnexpectedValueException
	 */
	public function onBeforeDelete($pk)
	{
		$aliasParts = explode('.', $this->contenthistoryHelper->typeAlias);
		if (JComponentHelper::getParams($aliasParts[0])->get('save_history', 0))
		{
			$this->parseTypeAlias();
			$this->contenthistoryHelper->deleteHistory($this->table);
		}
	}

	/**
	 * Internal method
	 * Parses a TypeAlias of the form "{variableName}.type", replacing {variableName} with table-instance variables variableName
	 * Storing result into $this->contenthistoryHelper->typeAlias
	 *
	 * @return  void
	 */
	protected function parseTypeAlias()
	{
		// Needed for PHP < 5.4.0 as it's not passing context $this to closure function
		static::$_myTableForPregreplaceOnly = $this->table;

		$this->contenthistoryHelper->typeAlias = preg_replace_callback('/{([^}]+)}/',
			function($matches)
			{
				return JTableObserverContenthistory::$_myTableForPregreplaceOnly->{$matches[1]};
			},
			$this->typeAliasPattern
		);
	}
}
