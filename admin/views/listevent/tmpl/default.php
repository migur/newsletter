<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset class="automailing-new">
<legend><?php echo JText::_('COM_NEWSLETTER_LIST_EVENT'); ?></legend>


	<form name="adminForm" method="POST" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter&tmpl=component'); ?>">

		<?php
		echo $this->form->getLabel('event');
		echo $this->form->getInput('event');

		echo $this->form->getLabel('jgroup_id');
		echo $this->form->getInput('jgroup_id');

		echo $this->form->getLabel('action');
		echo $this->form->getInput('action');

		echo $this->form->getInput('list_id');

		echo $this->form->getInput('le_id');
		?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="le_id" value="<?php echo $this->form->getValue('le_id'); ?>" />
		<input type="hidden" name="list_id" value="<?php echo JRequest::getInt('list_id', ''); ?>" />
		<?php echo JHtml::_('form.token'); ?>

		<div class="clr"></div>

		<?php echo MigurToolbar::getInstance('listevent')->render(); ?>

	</form>

</fieldset>
