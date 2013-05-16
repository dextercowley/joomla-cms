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
$object = json_decode($this->item->version_data);

?>
<h3>
<?php echo JText::sprintf('COM_CONTENTHISTORY_PREVIEW_SUBTITLE_DATE', $this->item->save_date); ?>
<?php if ($this->item->version_note) : ?>
	&nbsp;&nbsp;<?php echo JText::sprintf('COM_CONTENTHISTORY_PREVIEW_SUBTITLE', $this->item->version_note); ?>
<?php endif; ?>
</h3>
<table class="table table-striped" >
<thead><tr>
	<th><?php echo JText::_('COM_CONTENTHISTORY_PREVIEW_FIELD'); ?></th>
	<th><?php echo JText::_('COM_CONTENTHISTORY_PREVIEW_VALUE'); ?></th>
</tr></thead>
<tbody>
<?php foreach ($object as $name => $value) : ?>
	<tr><td><strong><?php echo $name; ?></strong></td><td><?php echo wordwrap($value, 80, ' ', true);?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
