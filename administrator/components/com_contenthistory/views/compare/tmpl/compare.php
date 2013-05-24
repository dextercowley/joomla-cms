<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
$version2 = $this->items[0];
$version1 = $this->items[1];
$object1 = $version1->data;
$object2 = $version2->data;
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
JHtml::_('textdiff.textdiff', 'diff');

JFactory::getDocument()->addScriptDeclaration("
			(function ($){
				$(document).ready(function (){
 					jQuery('.diffhtml, .diffhtml-header').hide();
 				});
			})(jQuery);
			"
 	);

?>
<fieldset>
<legend>
<?php echo JText::sprintf('COM_CONTENTHISTORY_COMPARE_TITLE'); ?>
&nbsp;<button id="toolbar-all-rows" class="btn hasTooltip" title="<?php echo JText::_('COM_CONTENTHISTORY_BUTTON_COMPARE_ALL_ROWS_DESC'); ?>"
	onclick="jQuery('.items-equal').show(); jQuery('#toolbar-all-rows').hide(); jQuery('#toolbar-changed-rows').show()"
	style="display:none" >
	<?php echo JText::_('COM_CONTENTHISTORY_BUTTON_COMPARE_ALL_ROWS'); ?></button>

<button id="toolbar-changed-rows" class="btn hasTooltip" title="<?php echo JText::_('COM_CONTENTHISTORY_BUTTON_COMPARE_CHANGED_ROWS_DESC'); ?>"
	onclick="jQuery('.items-equal').hide(); jQuery('#toolbar-all-rows').show(); jQuery('#toolbar-changed-rows').hide()">
	<?php echo JText::_('COM_CONTENTHISTORY_BUTTON_COMPARE_CHANGED_ROWS'); ?></button>

<button class="diff-header btn hasTooltip" title="<?php echo JText::_('COM_CONTENTHISTORY_BUTTON_COMPARE_HTML_DESC'); ?>"
	onclick="jQuery('.diffhtml, .diffhtml-header').show(); jQuery('.diff, .diff-header').hide()">
	<i class="icon-wrench"></i> <?php echo JText::_('COM_CONTENTHISTORY_BUTTON_COMPARE_HTML'); ?></button>

<button class="diffhtml-header btn hasTooltip" title="<?php echo JText::_('COM_CONTENTHISTORY_BUTTON_COMPARE_TEXT_DESC'); ?>"
	onclick="jQuery('.diffhtml, .diffhtml-header').hide(); jQuery('.diff, .diff-header').show()">
	<i class="icon-pencil"></i> <?php echo JText::_('COM_CONTENTHISTORY_BUTTON_COMPARE_TEXT'); ?></button>

</legend>
<table id="diff" class="table table-striped table-condensed">
<thead><tr>
	<th><?php echo JText::_('COM_CONTENTHISTORY_PREVIEW_FIELD'); ?></th>
	<th style="display:none" />
	<th style="display:none" />
	<th><?php echo JText::sprintf('COM_CONTENTHISTORY_COMPARE_VALUE1', $version1->save_date, $version1->version_note); ?></th>
	<th><?php echo JText::sprintf('COM_CONTENTHISTORY_COMPARE_VALUE2', $version2->save_date, $version2->version_note); ?></th>
	<th class="diff-header"><?php echo JText::_('COM_CONTENTHISTORY_COMPARE_DIFF'); ?></th>
	<th class="diffhtml-header"><?php echo JText::_('COM_CONTENTHISTORY_COMPARE_DIFF'); ?></th>
</tr></thead>
<tbody>
<?php foreach ($object1 as $name => $value) : ?>
	<?php $rowClass = ($value == $object2->$name) ? 'items-equal' : 'items-not-equal'; ?>
	<tr class="<?php echo $rowClass; ?>">
	<?php if (is_object($value)): ?>
		<td><strong><?php echo $name; ?></strong></td>
		<td /><td /><td />
		<?php foreach ($value as $subName => $subValue): ?>
			<?php $newSubValue = isset($object2->$name->$subName) ? $object2->$name->$subName : ''; ?>
			<?php if ($subValue || $newSubValue): ?>
				<?php $rowClass = ($subValue == $newSubValue) ? 'items-equal' : 'items-not-equal'; ?>
				<tr class="<?php echo $rowClass; ?>">
				<td><i>&nbsp;&nbsp;<?php echo $subName; ?></i></td>
				<td class="originalhtml" style="display:none" ><?php echo htmlspecialchars($subValue); ?></td>
				<td class="changedhtml" style="display:none" ><?php echo htmlspecialchars($newSubValue); ?></td>
				<td class="original"><?php echo $subValue; ?></td>
				<td class="changed"><?php echo $newSubValue; ?></td>
				<td class="diff" />
				<td class="diffhtml" />
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php else: ?>
		<td><strong><?php echo $name; ?></strong></td>
		<td class="originalhtml" style="display:none" ><?php echo htmlspecialchars($value); ?></td>
		<td class="changedhtml" style="display:none" ><?php echo htmlspecialchars($object2->$name); ?></td>
		<td class="original"><?php echo $value; ?></td>
		<td class="changed"><?php echo $object2->$name; ?></td>
		<td class="diff" />
		<td class="diffhtml" />
	<?php endif; ?>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
</fieldset>
