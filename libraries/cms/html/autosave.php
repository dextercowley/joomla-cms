<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Extended Utility class for auto save functionality
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       3.2
 */
abstract class JHtmlAutosave
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.2
	 */
	protected static $loaded = array();

	/**
	 * Method to load the Barchart script to display a bar chart using jQuery and jqPlot
	 *
	 * @param   integer  $seconds     Interval in seconds to perform auto save
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public static function autosave($seconds, $url)
	{
		// Only load once
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		JFactory::getDocument()->addScriptDeclaration("
			(function ($){
				$(document).ready(function (){
					var myAutoSave = setInterval(function(){autosave()}, " . (int) ($seconds * 1000) . ");

					function autosave()
					{
						$.ajax({
						url : '" . $url . "',
						type : 'post',
						dataType : 'json',
						success : onDataReceived,
						error: onError
						});

						function onDataReceived(data)
						{
							alert('success: ' + data.responseText);
						};
						function onError(data)
						{
							alert('error: ' + data.responseText);
						}
					}
				});
			})(jQuery);
			"
		);

		// Set static array
		self::$loaded[__METHOD__] = true;
		return;
	}

}
