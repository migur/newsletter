<div class="control-group">
	<div class="control-label">
		<label><?php echo JText::_('COM_NEWSLETTER_EXPORT_DATA_LABEL'); ?></label>
	</div>
	<div class="controls offset4">
		<input class="btn" type="button" id="export-button" value="<?php echo JText::_('COM_NEWSLETTER_EXPORT_DATA'); ?>" />
	</div>	
</div>

<div class="control-group">
	<div class="control-label">
		<label><?php echo JText::_('COM_NEWSLETTER_IMPORT_FROM_COMPONENTS'); ?></label>
	</div>
	<div class="controls offset4">
		<a 
			href="<?php echo JRoute::_('index.php?option=com_newsletter&tmpl=component&view=import'); ?>"
			data-toggle="migurmodal"
			data-target="#modal-import"
			class="btn"
		>
			<?php echo JText::_('COM_NEWSLETTER_IMPORT_DATA'); ?>
		</a>
	</div>	
</div>
