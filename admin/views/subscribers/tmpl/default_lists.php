<form id="form-lists" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=subscribers&form=lists', false);?>" method="post" name="listsForm">
    <fieldset>
        <legend><?php echo JText::_('COM_NEWSLETTER_LISTS'); ?></legend>
    	<fieldset class="filter-bar">
            <?php echo MigurToolBar::getInstance('lists')->render(); ?>
            <div id="lists-filter-panel-control" class="filter-panel-control"></div>
            <div class="clr"></div>
            <div id="lists-filter-panel" class="filter-panel">
				<div class="fltlft">
				<div class="label"><?php echo JText::_('COM_NEWSLETTER_STATE'); ?></div>
					<select name="filter_published" class="inputbox fltlt" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('multigrid.enabledOptions'), 'value', 'text', $this->lists->state->get('filter.published'), true);?>
					</select>
				</div>
				<div class="fltlft">
				<div class="label"><?php echo JText::_('COM_NEWSLETTER_FILTER'); ?></div>
					<input type="text" name="filter_search" id="lists_filter_search" class="migur-search" value="<?php echo $this->escape($this->lists->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_NEWSLETTER_FILTER_SEARCH_DESC'); ?>" />

					<button class="filter-search-button" type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					<button type="button" onclick="document.id('lists_filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				</div>
            </div>
	</fieldset>

	<table class="sslist adminlist">
		<thead>
			<tr>
				<th class="left" width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class="left">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_LIST_NAME', 'a.name', $this->lists->listDirn, $this->lists->listOrder, null, null, 'listsForm'); ?>
				</th>
				<th class="left" width="20%">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_SUBSCRIBERS', 'subscribers', $this->lists->listDirn, $this->lists->listOrder, null, null, 'listsForm'); ?>
				</th>
				<th class="left" width="15%">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_ACTIVATED', 'a.state', $this->lists->listDirn, $this->lists->listOrder, NULL, 'desc', 'listsForm'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->lists->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->lists->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
                                    <?php echo JHtml::_('multigrid.id', $i, $item->list_id, false, 'cid', 'listsForm'); ?>
				</td>
				<td>
				<?php 
				if (AclHelper::actionIsAllowed('list.edit')) { ?>
					<a href="<?php echo JRoute::_("index.php?option=com_newsletter&tmpl=component&layout=edit&task=list.edit&list_id=".(int) $item->list_id, false); ?>"
					   rel="{handler: 'iframe', size: {x: 990, y: 580}}"
					   class="modal" >
						<?php echo $this->escape($item->name); ?>
					</a>
				<?php } else { 
					echo $this->escape($item->name);
				}
				?>	
				</td>
				<td>
				<?php 
					if (intval($item->subscribers) > 0) {
						echo '<a href="#" onclick="document.subscribersForm.filter_list.value=\'' . $item->list_id . '\';document.subscribersForm.submit();">' . $this->escape(intval($item->subscribers)) . '</a>';
					} else {
						echo '<span style="color:#cccccc">0</span>';
					}
				?>
				</td>
				<td class="center">
					<?php echo JHtml::_('multigrid.enabled', $item->state, $i, 'tick.png', 'publish_x.png', 'lists.', 'listsForm'); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists->listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists->listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
    </fieldset>
</form>

