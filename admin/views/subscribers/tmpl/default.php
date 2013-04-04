<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen');
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
	<div class="subscribers-container">
		<?php echo $this->loadTemplate('subscribers'); ?>
	</div>
	<div class="lists-container">
		<?php echo $this->loadTemplate('lists'); ?>
	</div>
</div>

<?php echo JHtml::_('layout.wrapperEnd'); ?>

