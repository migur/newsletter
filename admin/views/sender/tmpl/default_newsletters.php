<fieldset id="newsletters-fieldset">
<legend><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER'); ?></legend>

	<?php echo JHtml::_('migurform.element', 'newsletters', 'newsletter-select', null, array('scope' => 'ordinary_unsent static')); ?>
</fieldset>