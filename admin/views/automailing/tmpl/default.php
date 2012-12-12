<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset class="automailing-new">
<legend><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING'); ?></legend>	

	<form name="automailingForm" method="POST" id="form-automailing" class="form-horizontal form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">

			<div class="control-group">
				<label class="control-label"><?php echo $this->form->getLabel('automailing_name'); ?></label>
                <div class="controls">
                    <?php echo $this->form->getInput('automailing_name'); ?>
                </div>    
			</div>

			<div class="control-group">
				<label class="control-label"><?php echo $this->form->getLabel('automailing_event'); ?></label>
				<div class="controls">
                    <?php echo $this->form->getInput('automailing_event'); ?>
                </div>    
			</div>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="automailing_id" value="0" />
		<?php echo JHtml::_('form.token'); ?>

    	<div class="form-actions">
            <?php echo JToolBar::getInstance()->render(); ?>
        </div>	
    </form>

</fieldset>
