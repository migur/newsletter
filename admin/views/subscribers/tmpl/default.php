<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen');
?>

<?php echo JHtml::_('layout.wrapper'); ?>

<div>
	<div class="subscribers-container">
		<?php echo $this->loadTemplate('subscribers'); ?>
	</div>
	<div class="lists-container">
		<?php echo $this->loadTemplate('lists'); ?>
	</div>
</div>

<?php echo JHtml::_('layout.wrapperEnd'); ?>
