<?php
// no direct access
defined('_JEXEC') or die;
?>

<form id="smtpprofile-form" class="form-validate form-horizontal" name="smtpprofileForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&layout=') . $this->getLayout(); ?>" method="post">

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('smtp_profile_name'), $this->ssForm->getInput('smtp_profile_name')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('from_name'), $this->ssForm->getInput('from_name')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('from_email'), $this->ssForm->getInput('from_email')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('reply_to_name'), $this->ssForm->getInput('reply_to_name')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('reply_to_email'), $this->ssForm->getInput('reply_to_email')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('smtp_server'), $this->ssForm->getInput('smtp_server')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('smtp_port'), $this->ssForm->getInput('smtp_port')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('username'), $this->ssForm->getInput('username')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('password'), $this->ssForm->getInput('password')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('is_ssl'), $this->ssForm->getInput('is_ssl')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('mailbox_profile_id'), $this->ssForm->getInput('mailbox_profile_id')); ?>

	<legend><?php echo JText::_('COM_NEWSLETTER_MAILING_PERIOD_CONFIGURATION'); ?></legend>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('sentsPerPeriodLimit', 'params'), $this->ssForm->getInput('sentsPerPeriodLimit', 'params')); ?>

	<?php echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('periodLength', 'params'), $this->ssForm->getInput('periodLength', 'params')); ?>

	<?php echo JHtml::_('layout.controlgroup', '', $this->ssForm->getInput('inProcess', 'params')); ?>

	<?php echo JHtml::_('layout.controlgroup', '', $this->ssForm->getInput('periodStartTime', 'params')); ?>

	<?php echo JHtml::_('layout.controlgroup', '', $this->ssForm->getInput('sentsPerLastPeriod', 'params')); ?>
		
	<?php echo JHtml::_('layout.controlgroup', '', $this->ssForm->getInput('smtp_profile_id')); ?>
	
	<div class="form-actions" style="padding-right:0;">
		<div class="pull-left"><?php echo JToolBar::getInstance()->render(); ?></div>
		<div class="pull-left" style="margin: 12px 0 0 10px;">
			<div id="preloader-container"></div>
		</div>	
	</div>

	<input type="hidden" name="smtp_profile_id" value="<?php echo $this->ssForm->getValue('smtp_profile_id'); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>
