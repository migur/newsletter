
<form action="<?php echo JRoute::_('index.php?option=com_newsletter&view=install');?>" method="post" name="adminForm" id="adminForm">

	<?php if (count($this->items)) : ?>
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th width="10">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_EXTENSION_TITLE', 'a.title', $this->listDirn, $this->listOrder, null, null, 'adminForm'); ?>
				</th>
				<th>
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_EXTENSION_CLASS', 'a.extension', $this->listDirn, $this->listOrder, null, null, 'adminForm'); ?>
				</th>
				<th>
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_EXTENSION_TYPE', 'a.type', $this->listDirn, $this->listOrder, null, null, 'adminForm'); ?>
				</th>
				<th>
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_EXTENSION_NAMESPACE', 'a.namespace', $this->listDirn, $this->listOrder, null, null, 'adminForm'); ?>
				</th>
				<th>
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_EXTENSION_ID', 'a.extension_id', $this->listDirn, $this->listOrder, null, null, 'adminForm'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo JHtml::_('grid.id', $i, $item->extension_id); ?>
				</td>
				<td>
						<?php echo $this->escape($item->title); ?>
				</td>
				<td>
						<?php echo $this->escape($item->extension); ?>
				</td>
				<td>
					<?php echo JText::_('COM_NEWSLETTER_EXTENSION_TYPE_' . $item->type); ?>
				</td>
				<td>
						<?php echo $this->escape($item->namespace); ?>
				</td>
				<td>
					<?php echo $item->extension_id ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="form" value="install" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
