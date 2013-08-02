<?php
// no direct access
defined('_JEXEC') or die;
?>

<?php echo JHtml::_('layout.wrapper'); ?>

<?php echo $this->loadTemplate('newsletters'); ?>

<br/>

<?php echo $this->loadTemplate('lists'); ?>

<div class="inline-info pull-left">
	<span class="label label-info">#3</span>
	<span class="text-info"><?php echo JText::_('COM_NEWSLETTER_SENDMAIL_STEP3'); ?></span>
</div>

<div class="pull-right">
	<button id="control-button-send" class="btn btn-large btn-success">
		<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SEND'); ?>
	</button>
	<?php echo JToolBar::getInstance('sender')->render(); ?>
</div>	

<div id="send-msg-container"class="pull-right">
	<div id="send-message" class="pull-left"></div>
	<div class="pull-left">&nbsp;&nbsp;&nbsp;</div>
	<div id="send-preloader" class="pull-left"></div>
</div>

<?php echo JHtml::_('layout.wrapperEnd'); ?>

