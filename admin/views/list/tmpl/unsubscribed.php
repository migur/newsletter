<div id="tab-container-unsubscribed">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="unsubscribed_filter_search" value="<?php //echo $this->escape($this->lists->state->get('filter.search')); ?>" title="<?php echo JText::_('com_newsletter_FILTER_SEARCH_DESC'); ?>" />
        </div>

        <table class="sslist adminlist  table table-striped" id="table-unsubscribed">
		<thead>
			<tr>
				<th width="30%" class="left">
					<?php echo JHtml::_('multigrid.sort', 'JGLOBAL_USERNAME', 'a.name', $this->subscribers->listDirn, $this->subscribers->listOrder, null, 'asc', 'listsForm'); ?>
				</th>
				<th width="20%" class="left">
					<?php echo JHtml::_('multigrid.sort', 'JGLOBAL_EMAIL', 'email', $this->subscribers->listDirn, $this->subscribers->listOrder, null, 'asc', 'listsForm'); ?>
				</th>
				<th width="20%" class="left">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_DATETIME', 'date', $this->subscribers->listDirn, $this->subscribers->listOrder, NULL, 'asc', 'subscribersForm'); ?>
				</th>
				<th class="left">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_REASON', 'reason', $this->subscribers->listDirn, $this->subscribers->listOrder, NULL, 'asc', 'subscribersForm'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
				</td>
			</tr>
		</tfoot>
		<tbody>

		<?php foreach ($this->subscribers->items as $i => $item) {
                    if (empty($item->list_id)) { ?>

			<tr class="row<?php echo $i % 2; ?>">
				<td>
                                    <?php echo $this->escape($item->name); ?>
				</td>
				<td>
                                    <?php echo $this->escape($item->email); ?>
				</td>
				<td>
                                    <?php echo $this->escape($item->date); ?>
				</td>
				<td>
                                    <?php echo $this->escape($item->text); ?>
				</td>
			</tr>
			<?php }} ?>
		</tbody>
	</table>
</div>