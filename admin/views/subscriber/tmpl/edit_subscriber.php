<?php
// no direct access
defined('_JEXEC') or die;
?>

<form id="subscriber-form" class="form-validate form-horizontal" name="subscriberForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&layout=') . $this->getLayout(); ?>" method="post">

	
	<?php 
	if (!$this->subscriber->isJoomlaUserType()) { 
		echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('name'), $this->ssForm->getInput('name'));
		echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('email'), $this->ssForm->getInput('email'));
	} else { 
		$name = $this->ssForm->getField('name');
		echo JHtml::_(
			'layout.controlgroup', 
			$this->ssForm->getLabel('name'), 
			'<span class="inputtext-replacer">'.$name->value.'</span><input type="hidden" value="'.$name->value.'" name="'.$name->name.'">'
		);
		$email = $this->ssForm->getField('email');
		echo JHtml::_(
			'layout.controlgroup', 
			$this->ssForm->getLabel('email'), 
			'<span class="inputtext-replacer">'.$email->value.'</span><input type="hidden" value="'.$email->value.'" name="'.$email->name.'">'
		);
	}
	
	echo JHtml::_('layout.controlgroup', $this->ssForm->getLabel('html'), $this->ssForm->getInput('html'));
	
	?>
	
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