<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JRequest::checkToken('get') or die(JText::_('JINVALID_TOKEN'));

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('jquery.framework');

$input = JFactory::getApplication()->input;
$field = $input->getCmd('field');
$function = 'jSelectContenthistory_'.$field;
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$message = JText::_('COM_CONTENTHISTORY_BUTTON_SELECT_ONE');
$compareMessage = JText::_('COM_CONTENTHISTORY_BUTTON_SELECT_TWO');
$aliasArray = explode('.', $this->state->type_alias);
$option = ($aliasArray[1] == 'category') ? 'com_categories&amp;extension=' . $aliasArray[0] : $aliasArray[0];
$filter = JFilterInput::getInstance();
$task = $filter->clean($aliasArray[1]) . '.loadhistory';
$loadUrl = JRoute::_('index.php?option=' . $filter->clean($option) . '&amp;task=' . $task);

JFactory::getDocument()->addScriptDeclaration("
	(function ($){
		$(document).ready(function (){
			$('#toolbar-load').click(function() {
				var ids = $('input:checked');
				if (ids.length == 1) {
					// Add version item id to URL
					var url = $('#toolbar-load').attr('data-url') + '&version_id=' + ids[0].value;
					$('#content-url').attr('data-url', url);
					if (window.parent) {
						window.parent.location = url;
					}
				} else {
					alert('" . $message . "');
				}
			});
			$('#toolbar-preview').click(function() {
				var windowSizeArray = ['width=800, height=600, scrollbars=yes'];
				var ids = $('input:checked');
				if (ids.length == 1) {
					// Add version item id to URL
					var url = $('#toolbar-preview').attr('data-url') + '&version_id=' + ids[0].value;
					$('#content-url').attr('data-url', url);
					if (window.parent) {
						window.open(url, '', windowSizeArray);
						event.preventDefault();
					}
				} else {
					alert('" . $message . "');
				}
			});
			$('#toolbar-compare').click(function() {
				var windowSizeArray = ['width=800, height=600, scrollbars=yes'];
				var ids = $('input:checked');
				if (ids.length == 2) {
					// Add version item ids to URL
					var url = $('#toolbar-compare').attr('data-url') + '&id1=' + ids[0].value + '&id2=' + ids[1].value;
					$('#content-url').attr('data-url', url);
					if (window.parent) {
						window.open(url, '', windowSizeArray);
						event.preventDefault();
					}
				} else {
					alert('" . $compareMessage . "');
				}
			});
		});
	})(jQuery);
	"
);

?>
<h3><?php echo JText::_('COM_CONTENTHISTORY_MODAL_TITLE'); ?></h3>
<div class="btn-group pull-right">
	<button id="toolbar-load" type="submit" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('COM_CONTENTHISTORY_BUTTON_LOAD_DESC'); ?>"
		data-url="<?php echo JRoute::_($loadUrl);?>" id="content-url">
		<span class="icon-upload"></span><?php echo '&#160;' . JText::_('COM_CONTENTHISTORY_BUTTON_LOAD'); ?></button>
	<button id="toolbar-preview" type="button" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('COM_CONTENTHISTORY_BUTTON_PREVIEW_DESC'); ?>"
		data-url="<?php echo JRoute::_('index.php?option=com_contenthistory&view=preview&layout=preview&tmpl=component&' . JSession::getFormToken() . '="1"');?>">
		<span class="icon-search"></span><?php echo '&#160;' . JText::_('COM_CONTENTHISTORY_BUTTON_PREVIEW'); ?></button>
	<button id="toolbar-compare" type="button" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('COM_CONTENTHISTORY_BUTTON_COMPARE_DESC'); ?>"
		data-url="<?php echo JRoute::_('index.php?option=com_contenthistory&view=compare&layout=compare&tmpl=component&' . JSession::getFormToken() . '="1"');?>">
		<span class="icon-zoom-in"></span><?php echo '&#160;' . JText::_('COM_CONTENTHISTORY_BUTTON_COMPARE'); ?></button>
</div>
<div class="clearfix"></div>
<form action="<?php echo JRoute::_('index.php?option=com_contenthistory&view=history&layout=modal&tmpl=component');?>" method="post" name="adminForm" id="adminForm">
<div id="j-main-container">
	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th width="1%" class="hidden-phone"/>
				<th width="15%">
					<?php echo JText::_('JDATE'); ?>
				</th>
				<th width="15%">
					<?php echo JText::_('COM_CONTENTHISTORY_VERSION_NAME'); ?>
				</th>
				<th width="15%">
					<?php echo JText::_('JAUTHOR'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('COM_CONTENTHISTORY_CHARACTER_COUNT'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$i = 0;
			foreach ($this->items as $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center hidden-phone">
					<?php echo JHtml::_('grid.id', $i, $item->version_id); ?>
				</td>
				<td align="left">
					<?php echo $item->save_date; ?></a>
				</td>
				<td align="left">
					<?php echo htmlspecialchars($item->version_note); ?>
				</td>
				<td align="left">
					<?php echo htmlspecialchars($item->editor); ?>
				</td>
				<td align="right">
					<?php echo number_format((int) $item->character_count, 0, '.', ','); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	</div>
</form>


