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
			
				<a
					id="ctrl-smtpprofile-new"
					class="btn btn-success"
					data-toggle="migurmodal" 
					data-target="#modal-smtpprofile"
					href="index.php?option=com_newsletter&view=smtpprofile&tmpl=component"
				>
					<?php echo JText::_('COM_NEWSLETTER_CREATE_SMTP_PROFILE'); ?>
				</a>

				<a
					id="ctrl-smtpprofile-edit"
					class="btn"
					data-toggle="migurmodal" 
					data-target="#modal-smtpprofile"
					data-href="index.php?option=com_newsletter&task=smtpprofile.edit&tmpl=component&smtp_profile_id="
				>
					<?php echo JText::_('COM_NEWSLETTER_EDIT'); ?>
				</a>

				<a
					id="ctrl-smtpprofile-delete"
					class="btn btn-danger"
				>
					<?php echo JText::_('COM_NEWSLETTER_DELETE_SMTP_PROFILE'); ?>
				</a>
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
			
			<a
				id="ctrl-mailboxprofile-new"
				class="btn btn-success"
				data-toggle="migurmodal" 
				data-target="#modal-mailboxprofile"
				href="index.php?option=com_newsletter&view=mailboxprofile&tmpl=component"
			>
				<?php echo JText::_('COM_NEWSLETTER_CREATE_MAILBOX_PROFILE'); ?>
			</a>

			<a
				id="ctrl-mailboxprofile-edit"
				class="btn"
				data-toggle="migurmodal" 
				data-target="#modal-mailboxprofile"
				data-href="index.php?option=com_newsletter&task=mailboxprofile.edit&tmpl=component&mailbox_profile_id="
			>
				<?php echo JText::_('COM_NEWSLETTER_EDIT'); ?>
			</a>

			<a
				id="ctrl-mailboxprofile-delete"
				class="btn btn-danger"
			>
				<?php echo JText::_('COM_NEWSLETTER_DELETE_MAILBOX_PROFILE'); ?>
			</a>
			
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
