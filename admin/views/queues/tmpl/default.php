<fieldset>
<legend><?php echo JText::_('Queues'); ?></legend>
<form id="form-queueslist" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=queues');?>" method="post" name="adminForm" >
	<fieldset id="filter-bar" >
            <?php echo JToolBar::getInstance('queues')->render(); ?>
            <div id="queues-filter-panel-control" class="filter-panel-control"></div>
            <div class="clr"></div>
            <div id="queues-filter-panel" class="filter-panel">
				<div class="fltlft">
					<input class="migur-search" type="text" name="filter_search" id="filter_search" class="filter-search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_NEWSLETTER_FILTER_SEARCH_DESC'); ?>" />
					<button type="submit" class="btn migur-search-submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				</div>
            </div>
	</fieldset>

	<div class="queueslist-container">
        <table class="queueslist adminlist" width="100%">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th width="15%" class="left">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_EMAIL', 's.name', $this->listDirn, $this->listOrder, NULL, 'desc'); ?>
				</th>
				<th width="15%" class="left">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_SUBSCRIBER_NAME', 's.email', $this->listDirn, $this->listOrder, NULL, 'desc'); ?>
				</th>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_NEWSLETTER_NAME', 'n.name', $this->listDirn, $this->listOrder, NULL, 'desc'); ?>
				</th>
				<th width="15%" class="left">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_QUEUE_CREATED', 'q.created', $this->listDirn, $this->listOrder, NULL, 'desc'); ?>
				</th>
				<th width="15%" class="left">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_STATE', 'q.state', $this->listDirn, $this->listOrder, NULL, 'desc'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php if(count($this->items) > 0) foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->queue_id); ?>
				</td>
				<td>
					<?php echo $this->escape($item->subscriber_email); ?>
				</td>
				<td>
					<?php echo $this->escape($item->subscriber_name); ?>
				</td>
				<td>
					<?php echo $this->escape($item->newsletter_name); ?>
				</td>
				<td>
					<?php echo $this->escape($item->created); ?>
				</td>
				<td>
					<?php 
						switch($item->state) {
							case 0: echo '<span style="color:green">'.JText::_('COM_NEWSLETTER_QUEUE_SENT').'</span>'; break; 
							case 1: echo '<span style="color:gray">'.JText::_('COM_NEWSLETTER_QUEUE_INPROGRESS').'</span>'; break;
							case 2: echo '<span style="color:red">'.JText::_('COM_NEWSLETTER_QUEUE_ERROR').'</span>'; break;
						}		
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	</div>	
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
</fieldset>