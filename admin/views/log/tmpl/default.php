<h3><?php echo JText::_('COM_NEWSLETTER_LOG_ITEM') . ": " . $this->item->date . ' - ' . $this->item->message; ?></h3>
<div style="height:650px;overflow: auto">
<table class="adminlist  table table-striped">
	<tbody>
		<?php 
		foreach($this->item->params as $name => $value) : 
			if (strtolower($name) == 'password') {
				$value = 'xxxxxx';
			}
		?>
			<tr>
				<td><?php echo $this->escape($name); ?></td>
				<td valign="top"><?php echo JHtml::_('multigrid.renderObject', $value); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>