<fieldset>
<legend><?php echo JText::_('Newsletters'); ?></legend>
<form id="form-newsletterslist" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=newsletters&form=newsletters');?>" method="post" name="adminForm" >
	<fieldset id="filter-bar" >
            <?php echo JToolBar::getInstance('newsletters')->render(); ?>
            <div id="newsletters-filter-panel-control" class="filter-panel-control"></div>
            <div class="clr"></div>
            <div id="newsletters-filter-panel" class="filter-panel">
				<div class="fltlft">
					<input class="migur-search" type="text" name="newsletters_filter_search" id="filter_search" class="filter-search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_NEWSLETTER_FILTER_SEARCH_DESC'); ?>" />
					<button type="submit" class="btn migur-search-submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					<button type="button" onclick="document.id('newsletters_filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				</div>
            </div>
	</fieldset>

        <table class="sslist adminlist" width="100%">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th width="39%" class="left">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_NEWSLETTER_NAME', 'n.name', $this->listDirn, $this->listOrder); ?>
				</th>
				<th width="20%" class="left">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_SENT_TO', 'sent_to', $this->listDirn, $this->listOrder, NULL, 'desc'); ?>
				</th>
				<th width="20%" class="left">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_START_DATE', 'n.sent_started', $this->listDirn, $this->listOrder, NULL, 'desc'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php if(count($this->items) > 0) foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
				<?php 
				if (AclHelper::actionIsAllowed('newsletter.edit')) { 
				?>
					<a href="<?php echo JRoute::_("index.php?option=com_newsletter&task=newsletter.edit&newsletter_id=" . (int) $item->id, false); ?>">
						<?php echo $this->escape($item->name); ?>
					</a>
				<?php 
				} else {	
					echo $this->escape($item->name); 
				}
				?>
				</td>
				<td>
					<?php
						if ($item->type == 0) {
							echo $item->sent_to;
						} else {
							echo '<span style="color:green;">' . JText::_('COM_NEWSLETTER_STATIC') . '</span>';
						}
					?>
				</td>
				<td>
					<?php echo $item->sent_started;?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
</fieldset>