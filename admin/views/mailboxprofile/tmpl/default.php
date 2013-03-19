<?php
// no direct access
defined('_JEXEC') or die;
?>

<form id="mailboxprofile-form" class="form-validate form-horizontal" name="mailboxprofileForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&layout=') . $this->getLayout(); ?>" method="post">

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('mailbox_profile_name'), $this->ssForm->getInput('mailbox_profile_name')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('mailbox_server'), $this->ssForm->getInput('mailbox_server')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('mailbox_port'), $this->ssForm->getInput('mailbox_port')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('mailbox_server_type'), $this->ssForm->getInput('mailbox_server_type')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('username'), $this->ssForm->getInput('username')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('password'), $this->ssForm->getInput('password')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('is_ssl'), $this->ssForm->getInput('is_ssl')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('validate_cert'), $this->ssForm->getInput('validate_cert')); ?>

	<div class="form-actions">
			<?php echo JToolBar::getInstance('mailbox-toolbar')->render(); ?>
	</div>

	<input type="hidden" name="mailbox_profile_id" value="<?php echo $this->ssForm->getValue('mailbox_profile_id'); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	
</form>