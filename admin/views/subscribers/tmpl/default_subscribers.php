<?php $filterList = $this->subscribers->state->get('filter.list'); ?>

<form id="form-subscribers" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=subscribers&form=subscribers');?>" method="post" name="subscribersForm" >
    <fieldset>
        <legend><?php echo JText::_('COM_NEWSLETTER_SUBSCRIBERS'); ?></legend>
	<fieldset class="filter-bar">
            <?php echo MigurToolBar::getInstance('subscribers')->render(); ?>
            <div id="ss-filter-panel-control" class="filter-panel-control"></div>
            <div class="clr"></div>
            <div id="ss-filter-panel" class="filter-panel <?php echo !empty($filterList)? 'opened' : ''; ?>">
				<div class="fltlft">
					<div class="label"><?php echo JText::_('COM_NEWSLETTER_STATE'); ?></div>
					<select name="filter_published" class="inputbox fltlt" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('multigrid.enabledOptions'), 'value', 'text', $this->subscribers->state->get('filter.published'), true);?>
					</select>
				</div>
				<div class="fltlft">
					<div class="label"><?php echo JText::_('COM_NEWSLETTER_LIST'); ?></div>

					<select name="filter_list" class="inputbox fltlt" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('COM_NEWSLETTER_FILTER_ON_LISTS');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('multigrid.listsOptions', $this->lists->items), 'value', 'text', $this->subscribers->state->get('filter.list'), true);?>
					</select>
				</div>
				<div class="fltlft">
					<div class="label"><?php echo JText::_('COM_NEWSLETTER_TYPE'); ?></div>

					<select name="filter_type" class="inputbox fltlt" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('COM_NEWSLETTER_FILTER_ON_TYPES');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('multigrid.typesOptions'), 'value', 'text', $this->subscribers->state->get('filter.type'), true);?>
					</select>
				</div>
				<div class="fltlft">
					<div class="label"><?php echo JText::_('COM_NEWSLETTER_FILTER'); ?></div>
					<input type="text" name="filter_search" id="ss_filter_search" class="migur-search" value="<?php echo $this->escape($this->subscribers->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_NEWSLETTER_FILTER_SEARCH_DESC'); ?>" />

					<button class="filter-search-button" type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					<button type="button" onclick="document.id('ss_filter_search').value='';document.subscribersForm.filter_list.value='';document.subscribersForm.filter_published.value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				</div>
            </div>
	</fieldset>

	<table class="sslist adminlist">
		<thead>
			<tr>
				<th class="left" width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class="left" width="40%">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_NAME', 'a.name', $this->subscribers->listDirn, $this->subscribers->listOrder, null, null, 'subscribersForm'); ?>
				</th>
				<th class="left">
					<?php echo JHtml::_('multigrid.sort', 'JGLOBAL_EMAIL', 'a.email', $this->subscribers->listDirn, $this->subscribers->listOrder, null, null, 'subscribersForm'); ?>
				</th>
				<th width="15%" class="left">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_ACTIVATED', 'a.state', $this->subscribers->listDirn, $this->subscribers->listOrder, NULL, 'desc', 'subscribersForm'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td class="left" colspan="4">
					<?php echo $this->subscribers->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$subscriber = $this->subscriberModel; 
		foreach ($this->subscribers->items as $i => $item) : 
			$subscriber->setFromArray($item);
		?>

			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo JHtml::_('multigrid.id', $i, $subscriber->getExtendedId(), false, 'cid', 'subscribersForm'); ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_newsletter&tmpl=component&layout=edit&task=subscriber.edit&subscriber_id='.$subscriber->getExtendedId()); ?>"
					   rel="{handler: 'iframe', size: {x: 965, y: 540}}"
					   class="modal" >
						<?php echo $this->escape($item->name); ?>
					</a>
					<div class="<?php echo $subscriber->isJoomlaUserType()? 'juser-type-icon' : 'subscriber-type-icon'; ?>"></div>
				</td>
				<td>
                                        <?php echo $this->escape($subscriber->email); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('multigrid.enabled', $subscriber->state, $i, 'tick.png', 'publish_x.png', 'subscribers.', 'subscribersForm'); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="list_id" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->subscribers->listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->subscribers->listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
    </fieldset>
</form>
