<?php
// no direct access
defined('_JEXEC') or die;
?>

<div id="subscriber-form-edit" class="width-100 fltlft">
	<fieldset class="adminform" id="subscriber-edit-main">
	   <legend><?php echo JText::_('COM_NEWSLETTER_INFORMATION'); ?></legend>
	   <?php echo $this->loadTemplate('subscriber'); ?>
	</fieldset>
	<fieldset class="adminform" id="subscriber-edit-lists">
	   <legend><?php echo JText::_('COM_NEWSLETTER_LISTS'); ?></legend>
	   <?php echo $this->loadTemplate('lists'); ?>
	</fieldset>
	<fieldset class="adminform" id="subscriber-edit-newsletters">
	   <legend><?php echo JText::_('COM_NEWSLETTER_NEWSLETTERS'); ?></legend>
	   <?php echo $this->loadTemplate('newsletters'); ?>
	</fieldset>
	<fieldset class="adminform" id="subscriber-edit-history">
	   <legend>
			<?php echo JText::_('COM_NEWSLETTER_HISTORY'); ?>
			<a target="_blank" href="<?php echo SupportHelper::getResourceUrl('subscriber', 'history'); ?>">(?)</a>
	   </legend>
	   <?php echo $this->loadTemplate('history'); ?>
	</fieldset>
</div>