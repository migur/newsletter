<?php
// no direct access
defined('_JEXEC') or die;
?>

<?php echo JHtml::_('layout.wrapper'); ?>

<div id="newsletters-list">
	<?php echo $this->loadTemplate('newsletters'); ?>
</div>
<div id="newsletters-statistics">
	<?php echo $this->loadTemplate('statistics'); ?>
</div>

<?php echo JHtml::_('layout.wrapperEnd'); ?>
