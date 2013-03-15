<?php $filterList = $this->subscribers->state->get('filter.list'); ?>

<form id="form-subscribers" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=subscribers&form=subscribers');?>" method="post" name="subscribersForm" >
    <fieldset>
        <legend><?php echo JText::_('COM_NEWSLETTER_SUBSCRIBERS'); ?></legend>
	<fieldset class="filter-bar">
		<div class="row-fluid">
			<div class="pull-right">
				<?php echo MigurToolBar::getInstance('subscribers')->render(); ?>
			</div>	
		
			<div id="ss-filter-panel-control" class="pull-left filter-panel-control" data-role="ctrl-container"></div>
		</div>

		<br/>

		<div id="ss-filter-panel" class="row-fluid filter-panel <?php echo !empty($filterList)? 'opened' : ''; ?>" data-role="panel-container">
			<div class="filter-panel-inner" data-role="panel-container-inner">
				<div class="pull-left btn-group">
					<!--<label><?php echo JText::_('COM_NEWSLETTER_STATE'); ?></label>-->
					<select name="filter_published" class="input-small" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('multigrid.enabledOptions'), 'value', 'text', $this->subscribers->state->get('filter.published'), true);?>
					</select>
				</div>
				<div class="pull-left btn-group">
					<!--<label><?php echo JText::_('COM_NEWSLETTER_LIST'); ?></label>-->

					<select name="filter_list" class="input-medium" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('COM_NEWSLETTER_FILTER_ON_LISTS');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('multigrid.listsOptions', $this->lists->items), 'value', 'text', $this->subscribers->state->get('filter.list'), true);?>
					</select>
				</div>
				<div class="pull-left btn-group">
					<!--<label><?php echo JText::_('COM_NEWSLETTER_TYPE'); ?></label>-->

					<select name="filter_type" class="input-medium" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('COM_NEWSLETTER_FILTER_ON_TYPES');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('multigrid.typesOptions'), 'value', 'text', $this->subscribers->state->get('filter.type'), true);?>
					</select>
				</div>
				<div class="pull-left btn-group">
					<!--<label><?php echo JText::_('COM_NEWSLETTER_JUSERGROUP'); ?></label>-->
					<?php echo JHtml::_('access.usergroup', 'filter_jusergroup', $this->subscribers->state->get('filter.jusergroup'), "onchange=\"document.subscribersForm.filter_type.value='2';this.form.submit();\"", true); ?>
				</div>	
				<div class="pull-left btn-group">
					<div class="filter-search btn-group pull-left">
						<input type="text" name="filter_search" id="ss_filter_search" class="migur-search" value="<?php echo $this->escape($this->subscribers->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_NEWSLETTER_FILTER_SEARCH_DESC'); ?>" />		
					</div>	
					<div class="btn-group pull-left">
						<button class="btn tip filter-search-button" type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
						<button class="btn tip" type="button" onclick="document.id('ss_filter_search').value='';document.subscribersForm.filter_list.value='';document.subscribersForm.filter_published.value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
					</div>	
				</div>
			</div>
		</div>	
	</fieldset>

	<table class="sslist adminlist  table table-striped">
		<thead>
			<tr>
				<th class="left" width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="left" width="30%">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_NAME', 'a.name', $this->subscribers->listDirn, $this->subscribers->listOrder, null, null, 'subscribersForm'); ?>
				</th>
				<th class="left">
					<?php echo JHtml::_('multigrid.sort', 'JGLOBAL_EMAIL', 'a.email', $this->subscribers->listDirn, $this->subscribers->listOrder, null, null, 'subscribersForm'); ?>
				</th>
				<th class="left" width="20%">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_REGISTRATION_DATE', 'a.registerDate', $this->subscribers->listDirn, $this->subscribers->listOrder, null, null, 'subscribersForm'); ?>
				</th>
				<th class="left" width="12%">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_ENABLED', 'a.state', $this->subscribers->listDirn, $this->subscribers->listOrder, NULL, 'desc', 'subscribersForm'); ?>
				</th>
				<th class="left" width="12%">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_ACTIVATED', 'a.confirmed', $this->subscribers->listDirn, $this->subscribers->listOrder, NULL, 'desc', 'subscribersForm'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td class="left" colspan="6">
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
				<td class="subscriber-name">
					<?php 
						if (!$subscriber->subscriber_id) { 
							$href = JRoute::_('index.php?option=com_newsletter&layout=edit&task=subscriber.edit&user_id='.$subscriber->user_id, false);
						} else {
							$href = JRoute::_('index.php?option=com_newsletter&layout=edit&task=subscriber.edit&subscriber_id='.$subscriber->subscriber_id, false);
						}
					?>
					<a 
						href="<?php echo $href; ?>"
						class="subscriber-item" 
					>
						<?php echo $this->escape($item->name); ?>
					</a>
					
					<div class="<?php echo $subscriber->isJoomlaUserType()? 'juser-type-icon' : 'subscriber-type-icon'; ?>"></div>
				</td>
				<td class="subscriber-email">
					<?php echo $this->escape($subscriber->email); ?>
				</td>
				<td class="subscriber-registerDate">
					<?php echo $this->escape($subscriber->registerDate); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('multigrid.enabled', $subscriber->state, $i, 'tick.png', 'publish_x.png', 'subscribers.', 'subscribersForm'); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('multigrid.confirmed', $subscriber->confirmed, $i, 'tick.png', 'publish_x.png', 'subscribers.', 'subscribersForm'); ?>
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

