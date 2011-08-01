<table class="adminlist" width="100%">
	<thead>
		<tr>
			<th width="40%" class="left">
				<?php echo JText::_('COM_NEWSLETTER_NAME'); ?>
			</th>
			<th width="40%" class="left">
				<?php echo JText::_('COM_NEWSLETTER_AUTHOR'); ?>
			</th>
			<th width="20%" class="left">
				<?php echo JText::_('COM_NEWSLETTER_ACTIONS'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->modules as $i => $item) : ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td>
				<?php echo $this->escape($item->xml->name); ?>
				</td>
				<td>
				<?php echo $this->escape($item->xml->author); ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_newsletter&tmpl=component&view=extension&extension_id=' . (int) $item->extension_id . '&native=' . (int) $item->native); ?>"
					   rel="{ handler: 'iframe', size: {x: 820, y: 370} }"
					   class="modal" >

					<?php echo JText::_('COM_NEWSLETTER_INFO'); ?>
				</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
