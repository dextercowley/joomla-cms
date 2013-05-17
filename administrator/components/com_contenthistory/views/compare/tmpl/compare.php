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
$object1 = json_decode($version1->version_data);
$object2 = json_decode($version2->version_data);
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('textdiff.textdiff', 'diff');

?>
<fieldset style="width:500;">
<legend>
<?php echo JText::sprintf('COM_CONTENTHISTORY_COMPARE_TITLE'); ?>
</legend>
<table id="diff" class="table table-striped table-condensed">
<thead><tr>
	<th><?php echo JText::_('COM_CONTENTHISTORY_PREVIEW_FIELD'); ?></th>
	<th><?php echo JText::sprintf('COM_CONTENTHISTORY_COMPARE_VALUE1', $version1->save_date, $version1->version_note); ?></th>
	<th><?php echo JText::sprintf('COM_CONTENTHISTORY_COMPARE_VALUE2', $version2->save_date, $version2->version_note); ?></th>
	<th><?php echo JText::_('COM_CONTENTHISTORY_COMPARE_DIFF'); ?></th>
</tr></thead>
<tbody>
<?php foreach ($object1 as $name => $value) : ?>
	<?php $breakWordsOriginal = strpos($value, ' ') || (strlen($value <= 40)); ?>
	<?php $breakWordsChanged = strpos($object2->$name, ' ') || (strlen($object2->$name <= 40)); ?>
	<tr>
		<td><strong><?php echo $name; ?></strong></td>
		<td class="original"><?php echo wordwrap($value, 40, ' ', $breakWordsOriginal);?></td>
		<td class="changed"><?php echo wordwrap($object2->$name, 40, ' ', $breakWordsChanged);?></td>
		<td class="diff" />
	</tr>
<?php endforeach; ?>
</tbody>
</table>
</fieldset>
