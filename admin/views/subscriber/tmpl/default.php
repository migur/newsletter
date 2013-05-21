<?php
// no direct access
defined('_JEXEC') or die;
?>

<form id="subscriber-form" class="form-horizontal form-validate" name="subscriberForm" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>" method="post">

	<div class="control-group">
		<label class="control-label" for="jform-name"><?php echo $this->ssForm->getLabel('name'); ?></label>
		<div class="controls">
			<?php echo $this->ssForm->getInput('name'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="jform-email"><?php echo $this->ssForm->getLabel('email'); ?></label>
		<div class="controls">
			<?php echo $this->ssForm->getInput('email'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="jform-html"><?php echo $this->ssForm->getLabel('html'); ?></label>
		<div class="controls">
			<?php echo $this->ssForm->getInput('html'); ?>
		</div>
	</div>

	<?php echo $this->ssForm->getInput('subscriber_id'); ?>
	<?php echo $this->ssForm->getInput('confirmed'); ?>

	<input type="hidden" name="subscriber_id" value="<?php echo $this->ssForm->getValue('subscriber_id'); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

	<div class="form-actions">
		<?php echo JToolBar::getInstance()->render(); ?>
	</div>

</form>
