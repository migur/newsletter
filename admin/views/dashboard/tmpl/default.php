<?php
// no direct access
defined('_JEXEC') or die;
?>

<?php echo JHtml::_('layout.wrapper'); ?>

<div>
	<div class="container" id="dashboard-handlers">
		<?php echo $this->loadTemplate('handlers', ''); ?>
	</div>

	<div class="container" id="dashboard-static">
		<?php echo $this->loadTemplate('static', ''); ?>
	</div>
</div>	

<?php echo JHtml::_('layout.wrapperEnd'); ?>
