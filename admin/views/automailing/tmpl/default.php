<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="automailing-new">

	<form name="automailingForm" method="POST" id="form-automailing" class="form-horizontal form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">

		<?php echo JHtml::_('layout.controlgroup', $this->form->getLabel('automailing_name'), $this->form->getInput('automailing_name')); ?>

		<?php echo JHtml::_('layout.controlgroup', $this->form->getLabel('automailing_event'), $this->form->getInput('automailing_event')); ?>

		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="layout" value="default" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="automailing_id" value="0" />
		<?php echo JHtml::_('form.token'); ?>

    	<div class="form-actions">
            <?php echo MigurToolbar::getInstance()->render(); ?>
        </div>
    </form>

</div>
