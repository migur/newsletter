<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('general_send_disable'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('general_send_disable'); ?>
	</div>	
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('general_reg_disable'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('general_reg_disable'); ?>
	</div>	
</div>
<div class="control-group">
	<div class="control-label">
		<div class="pull-left"><?php echo $this->form->getLabel('general_smtp_default'); ?></div>
		<div class="pull-left"><?php echo JHtml::_('migurhelp.link', 'smtpp', 'general', 'smtpp-default'); ?></div>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('general_smtp_default'); ?>
		<div class="btn-group pull-right">
			<?php echo $this->form->getInput('newsletter_smtp_edit'); ?>
			<?php echo $this->form->getInput('newsletter_smtp_create'); ?>
			<?php echo $this->form->getInput('newsletter_smtp_delete'); ?>
		</div>
	</div>	
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('general_mailbox_default'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('general_mailbox_default'); ?>
		<div class="btn-group pull-right">
			<?php echo $this->form->getInput('newsletter_mailbox_edit'); ?>
			<?php echo $this->form->getInput('newsletter_mailbox_create'); ?>
			<?php echo $this->form->getInput('newsletter_mailbox_delete'); ?>
		</div>	
	</div>	
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('autosaver_enabled'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('autosaver_enabled'); ?>
	</div>	
</div>
<div class="control-group">
	<div class="control-label">
		<div class="pull-left"><?php echo $this->form->getLabel('confirm_mail_subject'); ?></div>
		<div class="pull-left"><?php echo JHtml::_('migurhelp.link', 'subscriber', 'subscription'); ?></div>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('confirm_mail_subject'); ?>
	</div>	
</div>
<div class="control-group">
	<div class="control-label">
		<div class="pull-left"><?php echo $this->form->getLabel('confirm_mail_body'); ?></div>
		<div class="pull-left"><?php echo JHtml::_('migurhelp.link', 'subscriber', 'subscription'); ?></div>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('confirm_mail_body'); ?>
	</div>	
</div>
