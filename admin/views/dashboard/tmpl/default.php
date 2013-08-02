<?php
// no direct access
defined('_JEXEC') or die;
?>

<div id="modal-subscriber" class="modal hide fade">
	<div class="modal-header">
		<button data-dismiss="modal" class="close" type="button">x</button>
		<h3><?php echo JText::_('COM_NEWSLETTER_NEW_SUBSCRIBER'); ?></h3>
	</div>
	<div class="modal-body"></div>
</div>

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
