<?php
// no direct access
defined('_JEXEC') or die;
?>

<form id="subscriber-form" class="form-validate form-horizontal" name="subscriberForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&layout=') . $this->getLayout(); ?>" method="post">
	
	<div class="control-group">
		<label class="control-label" for="jform-name"><?php echo $this->ssForm->getLabel('name'); ?></label>
		<div class="controls">
			<?php 
			if ($this->subscriber->isJoomlaUserType()) { 
				$name = $this->ssForm->getField('name');
			?>
				<span class="inputtext-replacer"><?php echo $name->value; ?></span>
				<input type="hidden" value="<?php echo $name->value; ?>" name="<?php echo $name->name; ?>">
			<?php } else { 
				echo $this->ssForm->getInput('name'); 
			} ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="jform-email"><?php echo $this->ssForm->getLabel('email'); ?></label>
		<div class="controls">
			<?php 
			if ($this->subscriber->isJoomlaUserType()) { 
				$email = $this->ssForm->getField('email');
			?>
				<span class="inputtext-replacer"><?php echo $email->value; ?></span>
				<input type="hidden" value="<?php echo $email->value; ?>" name="<?php echo $email->name; ?>">
			<?php } else { 
				echo $this->ssForm->getInput('email'); 
			} ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="jform-html"><?php echo $this->ssForm->getLabel('html'); ?></label>
		<div class="controls">
			<?php echo $this->ssForm->getInput('html'); ?>
		</div>
	</div>
	
	<div id="usertype-container">
		
		<div style="float:left" class="<?php echo $this->subscriber->isJoomlaUserType()? 'juser-type-icon' : 'subscriber-type-icon'; ?>"></div>
		<span><?php echo JText::_('COM_NEWSLETTER_'. ($this->subscriber->isJoomlaUserType()? 'JUSER' : 'MIGUR').'TYPE_SUBSCRIBER');	?></span>
		
		<?php if ($this->subscriber->isJoomlaUserType()) { ?>
		&nbsp;&nbsp;&nbsp;<span class="small">
			<?php echo JText::_('COM_NEWSLETTER_JUSER_CANT_EDIT'); ?>
		</span>
		<?php } ?>
	</div>

    <div class="buttons-container pull-right">
            <?php echo JToolBar::getInstance('subscriber-toolbar')->render(); ?>
    </div>

    <div>
            <?php echo $this->ssForm->getInput('subscriber_id'); ?>
            <?php echo $this->ssForm->getInput('user_id'); ?>
            <?php echo $this->ssForm->getInput('confirmed'); ?>
            <input type="hidden" name="subscriber_id" value="<?php echo $this->ssForm->getValue('subscriber_id'); ?>" />
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
    </div>

</form>