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
	 * Method to load the script to save the form using jQuery AJAX
	 *
	 * @param   string  $editorId   HTML id for the editor element on the page
	 * @param   string  $url        URL for the item being edited
	 * @param   string  $task       task for the auto save for this component
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public static function autosave($editorId, $url, $task)
	{
		// Only load once
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		// Only run if auto save is enabled globally and for this user
		$user = JFactory::getUser();
		$autoSave = JFactory::getApplication()->getCfg('auto_save', 0);
		$autoSaveMinutes = $user->getParam('auto_save_time', 0);
		if (!$autoSave || !$autoSaveMinutes)
		{
			return;
		}

		$getContentMethod = JFactory::getEditor()->getContent($editorId);

		JHtml::_('behavior.framework');
		JFactory::getDocument()->addScriptDeclaration("
			(function ($){
				$(document).ready(function (){
					$('input[name=task]').val('" . $task . "');

				    var myAutoSave = setInterval(function(){autosave()}, " . (int) ($autoSaveMinutes * 1000 * 60) . ");

				    // Comment the previous line and uncomment this line to test with short timeouts
				    // Use setTimeout to just do the save once
					// var myAutoSave = setTimeout(function(){autosave()}, " . (int) ($autoSaveMinutes * 1000 * 10) . ");

					function autosave()
					{
						var elements = $('form[name=adminForm]');
						var editor = $('iframe').contents().find('#tinymce');
						var editText = $('#" . $editorId . "');
						var editorContent = " . $getContentMethod . "
						editText.html(editorContent);
						var myData = elements.serialize();
						$.ajax({
						url : '" . $url . "',
						type : 'POST',
						dataType : 'json',
						data: myData,
						success : onDataReceived,
						error: onError
						});
						return false;

						function onDataReceived(data)
						{
							Joomla.renderMessages(data.messages);
						}
						function onError(data)
						{
							Joomla.renderMessages(data.messages);
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
