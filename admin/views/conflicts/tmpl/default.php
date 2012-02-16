<?php $filterList = $this->state->get('filter.list'); ?>

<form id="form-subscribers" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=conflicts&form=conflicts');?>" method="post" name="adminForm" >
    <fieldset>
        <legend><?php echo JText::_('COM_NEWSLETTER_SUBSCRIBERS'); ?></legend>
	<table class="sslist adminlist">
		<thead>
			<tr>
				<th class="left" width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class="left" width="24%">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_NAME', 'subName', $this->listDirn, $this->listOrder, null, null, 'subscribersForm'); ?>
					<div class="subscriber-type-icon" style="float:left"></div>
				</th>
				<th class="left" width="25%">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_EMAIL', 'subEmail', $this->listDirn, $this->listOrder, null, null, 'subscribersForm'); ?>
					<div class="subscriber-type-icon" style="float:left"></div>
				</th>
				<th class="left" width="25%">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_NAME', 'userName', $this->listDirn, $this->listOrder, null, null, 'subscribersForm'); ?>
					<div class="juser-type-icon" style="float:left"></div>
				</th>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_EMAIL', 'userEmail', $this->listDirn, $this->listOrder, null, null, 'subscribersForm'); ?>
					<div class="juser-type-icon" style="float:left"></div>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td class="left" colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		foreach ($this->items as $i => $item) : ?>

			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo JHtml::_('grid.id', $i, $item->subSubId, false, 'cid', 'subscribersForm'); ?>
				</td>
				<td>
					<?php echo $this->escape($item->subName); ?>
				</td>
				<td>
					<?php echo $this->escape($item->subEmail); ?>
				</td>
				<td>
					<?php echo $this->escape($item->userName); ?>
				</td>
				<td>
					<?php echo $this->escape($item->userEmail); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="list_id" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
    </fieldset>
</form>