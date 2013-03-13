<fieldset id="newsletters-fieldset">
<legend><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER'); ?></legend>
	<div class="inline-info">
		<span class="label label-info">#1</span>
		<span class="text-info"><?php echo JText::_('COM_NEWSLETTER_SENDMAIL_STEP1'); ?></span>
	</div>	

	<?php echo JHtml::_('migurform.element', 'newsletters', 'newsletter-select', null, array('scope' => 'ordinary_unsent static')); ?>
</fieldset>