<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset class="automailing-new">
<legend><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING'); ?></legend>	

	<form name="automailingForm" method="POST" id="form-automailing" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">

			<div>
				<?php echo $this->form->getLabel('automailing_name'); ?>
				<?php echo $this->form->getInput('automailing_name'); ?>
			</div>

			<div>
				<?php echo $this->form->getLabel('automailing_event'); ?>
				<?php echo $this->form->getInput('automailing_event'); ?>
			</div>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="automailing_id" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</form>

	<div class="clr"></div>
	<div id="automailing-wrapper">
		<?php echo JToolBar::getInstance('automailing')->render(); ?>
	</div>	

</fieldset>
