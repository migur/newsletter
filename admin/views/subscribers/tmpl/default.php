<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen');
?>

<!--<div id="modal-subscriberedit" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Modal header</h3>
  </div>
  <div class="modal-body">
	  <iframe></iframe>
  </div>
</div>-->

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

