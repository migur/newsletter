<form id="form-subscribers" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=subscribers&form=subscribers'); ?>" method="post" name="subscribersForm" >

	<table class="sslist" width="100%">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th width="40%" class="left">
					<?php echo JHtml::_('multigrid.sort', 'JGLOBAL_USERNAME', 'a.name', $this->subscribers->listDirn, $this->subscribers->listOrder, null, null, 'subscribersForm'); ?>
				</th>
				<th class="left">
					<?php echo JHtml::_('multigrid.sort', 'JGLOBAL_EMAIL', 'a.email', $this->subscribers->listDirn, $this->subscribers->listOrder, null, null, 'subscribersForm'); ?>
				</th>
				<th width="10%" class="left">
					<?php echo JHtml::_('multigrid.sort', 'JENABLED', 'a.state', $this->subscribers->listDirn, $this->subscribers->listOrder, NULL, 'desc', 'subscribersForm'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->subscribers->items as $i => $item) : ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center">
					<?php echo JHtml::_('multigrid.id', $i, $item->id, false, 'cid', 'subscribersForm'); ?>
					</td>
					<td class="center">
						<a href="<?php echo JRoute::_('index.php?option=com_newsletter&tmpl=component&layout=edit&task=subscriber.edit&id=' . (int) $item->id); ?>"
						   rel="{handler: 'iframe', size: {x: 1000, y: 700}, onClose: function() {}}"
						   class="modal" >
						   <?php echo $this->escape($item->name); ?>
					</a>
				</td>
				<td class="center">
					<?php echo $this->escape($item->email); ?>
	   				</td>
	   				<td class="center">
					<?php echo JHtml::_('multigrid.enabled', $item->state, $i, 'tick.png', 'publish_x.png', 'subscribers.', 'subscribersForm'); ?>
	   				</td>
	   			</tr>
			<?php endforeach; ?>
			   		</tbody>
			   	</table>

			   	<div>
			   		<input type="hidden" name="task" value="" />
			   		<input type="hidden" name="boxchecked" value="0" />
			   		<input type="hidden" name="filter_order" value="<?php echo $this->subscribers->listOrder; ?>" />
			   		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->subscribers->listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
