<?php
/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die; ?>

<?php 
if (empty($this->subscriber->subscriber_id)) {
	echo JText::_('COM_NEWSLETTER_YOU_ARE_NOT_SUBSCRIBER');

//	<fieldset style="width:300px; margin: auto">
//		<legend><?php echo JText::_('COM_NEWSLETTER_YOU_CAN_SUBSCRIBE'); </legend>
	
		
//		$module = JModuleHelper::getModule('mod_newsletter_subscribe');
//		echo JModuleHelper::renderModule($module); 
	
//	</fieldset>	


} else { 
?>	

	<span><?php echo JText::_('COM_NEWSLETTER_SUBSCRIBER_INFORMATION'). ":"; ?></span>
	<div class="subscriber-info">
		<div>
			<span><b><?php echo JText::_('COM_NEWSLETTER_EMAIL'); ?></b></span>
			<span><?php echo $this->escape($this->subscriber->email); ?></span>
		</div>
		<div>
			<span><b><?php echo JText::_('COM_NEWSLETTER_NAME'); ?></b></span>
			<span><?php echo $this->escape($this->subscriber->name); ?></span>
		</div>
	</div>

<?php 
	if (count($this->lists) < 1) { 
		echo JText::_('COM_NEWSLETTER_NOTHING_TO_UNSUBSCRIBE');
	} else { 
?>
	
<div class="newsletter<?php echo $moduleclass_sfx; ?>">
	<form class="mod-newsletter" action="<?php echo JRoute::_('index.php?option=com_newsletter&task=subscribe.unsubscribe'); ?>" method="POST" name="unsubscribe-form">
		<?php if (count($this->lists) > 1) { ?>
			<label for="newsletter-lists"><?php echo JText::_('COM_NEWSLETTER_SELECT_LIST_TO_UNSUBSCRIBE'); ?></label>
		<?php } else { ?>
			<?php echo JText::_('COM_NEWSLETTER_LIST_TO_UNSUBSCRIBE'). ":"; ?><br>
		<?php } ?>

		<table>
			<thead>
				<tr>
					<th><?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE'); ?></th>
					<th><?php echo JText::_('COM_NEWSLETTER_SELECT_LIST_NAME'); ?></th>
					<th><?php echo JText::_('COM_NEWSLETTER_SELECT_LIST_DESCRIPTION'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->lists as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td align="center">
						<input type="checkbox" name="newsletter-lists[]" value="<?php echo $item->list_id; ?>" />
					</td>
					<td>
						<?php echo $this->escape($item->name); ?>
					<td>
						<?php echo $this->escape($item->description); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<br/>
		<div>
			<input
				type="submit"
				value="<?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBE'); ?>"
			/>
		</div>
		<?php echo JHtml::_('form.token'); ?>
		<input name="newsletter-uid" type="hidden" value="<?php echo $this->uid; ?>" />
		<input name="newsletter-nid" type="hidden" value="<?php echo $this->nid; ?>" />
	</form>
</div>

<script text="javascript">
    migurName = "<?php echo $userName; ?>";
    migurEmail = "<?php echo $userEmail; ?>";
</script>
<?php
} }
?>