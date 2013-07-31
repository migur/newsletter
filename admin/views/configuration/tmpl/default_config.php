<form id="form-lists" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=subscribers&form=lists'); ?>" method="post" name="listsForm">
	<fieldset id="filter-bar" >
		<div class="filter-select fltrt" >

			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
				<?php echo JHtml::_('select.options', JHtml::_('multigrid.enabledOptions'), 'value', 'text', $this->lists->state->get('filter.published'), true); ?>
			</select>
		</div>

		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" class="migur-search" id="lists_filter_search" value="<?php echo $this->escape($this->lists->state->get('filter.search')); ?>" title="<?php echo JText::_('com_newsletter_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('lists_filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<?php echo MigurToolBar::getInstance('lists')->render(); ?>

			</fieldset>
			<div class="clr"> </div>

			<table class="sslist">
				<thead>
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
						</th>
						<th class="left">
					<?php echo JHtml::_('multigrid.sort', 'JGLOBAL_USERNAME', 'a.name', $this->lists->listDirn, $this->lists->listOrder, null, null, 'listsForm'); ?>
				</th>
				<th width="10%" class="left">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_SUBSCRIBERS', 'a.subscribers', $this->lists->listDirn, $this->lists->listOrder, null, null, 'listsForm'); ?>
				</th>
				<th width="5%" class="left">
					<?php echo JHtml::_('multigrid.sort', 'JENABLED', 'a.state', $this->lists->listDirn, $this->lists->listOrder, NULL, 'desc', 'listsForm'); ?>
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
					<td class="center">
					<?php echo JHtml::_('multigrid.id', $i, $item->id, false, 'cid', 'listsForm'); ?>
					</td>
					<td class="center">
					<?php echo $this->escape($item->name); ?>
					</td>
					<td class="center">
					<?php echo $this->escape(rand(0, 100)); ?>
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
</form>
