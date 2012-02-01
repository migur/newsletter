<table class="adminlist" width="100%">
	<thead>
		<tr>
			<th width="40%" class="left">
			</th>
			<th width="40%" class="left">
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<label><?php echo JText::_('COM_NEWSLETTER_EXPORT_DATA_LABEL'); ?></label>
			</td>
			<td>
				<input type="button" id="export-button" value="<?php echo JText::_('COM_NEWSLETTER_EXPORT_DATA'); ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<label><?php echo JText::_('COM_NEWSLETTER_IMPORT_FROM_COMPONENTS'); ?></label>
			</td>
			<td>
				<a href="<?php echo JRoute::_('index.php?option=com_newsletter&tmpl=component&view=import'); ?>"
				   rel="{handler:'iframe',size:{x: 500, y: 320}}"
				   class="modal" >
					<?php echo JText::_('COM_NEWSLETTER_IMPORT_DATA'); ?>
				</a>
			</td>
		</tr>
	</tbody>
</table>