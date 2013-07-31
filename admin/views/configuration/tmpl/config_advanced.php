
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('license_key'); ?>
	</div>
	<div class="controls offset4">
		<?php echo $this->form->getInput('license_key'); ?>
	</div>	
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('fbappid'); ?>
	</div>	
	<div class="controls offset4">
		<?php echo $this->form->getInput('fbappid'); ?>
	</div>	
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('fbsecret'); ?>
	</div>	
	<div class="controls offset4">
		<?php echo $this->form->getInput('fbsecret'); ?>
	</div>	
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('debug'); ?>
	</div>	
	<div class="controls offset4">
		<?php echo $this->form->getInput('debug'); ?>
	</div>	
</div>
<div class="control-group">
	<div class="control-label">
		<label><?php echo JText::_('COM_NEWSLETTER_MAINTAINANCE_DESC'); ?></label>
	</div>	
	<div class="controls offset4">
	
		<a 
			class="btn"
			data-toggle="migurmodal"
			data-target="#modal-import"
			href="<?php echo JRoute::_('index.php?option=com_newsletter&view=maintainance&tmpl=component', true); ?>">

			<?php echo JText::_('COM_NEWSLETTER_MAINTAINANCE'); ?>	
		</a>	
	</div>	
	
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('rawurls'); ?>
	</div>	
	<div class="controls offset4">
		<?php echo $this->form->getInput('rawurls'); ?>
	</div>	
</div>
		
<?php echo $this->form->getInput('monster_url'); ?>
<?php echo $this->form->getInput('product'); ?>
<?php echo $this->form->getInput('domain'); ?>