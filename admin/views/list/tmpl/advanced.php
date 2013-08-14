<div id="tab-container-advanced">
	<dl>
		<dt>
		<label>
			<?php echo JText::_('COM_NEWSLETTER_AUTOCONFIRM_USERS')//echo $this->listForm->getLabel('autoconfirm'); ?>
			<?php echo JHtml::_('migurhelp.link', 'subscriber/subscription/autoconfirm'); ?>
		</label>
		</dt>
		<dd>
			<?php echo $this->listForm->getInput('autoconfirm'); ?>
		</dd>
		<dt>
		<?php echo $this->listForm->getLabel('send_at_reg'); ?>
		</dt>
		<dd>
			<?php echo $this->listForm->getInput('send_at_reg'); ?>
		</dd>
		<dt>
		<?php echo $this->listForm->getLabel('send_at_unsubscribe'); ?>
		</dt>
		<dd>
			<?php echo $this->listForm->getInput('send_at_unsubscribe'); ?>
		</dd>
	</dl>

<!--		<input name="jform[events]" value="<?php echo json_encode($this->events); ?>" type="hidden" />-->
	<div class="clr"></div>

	<br/><br/>
	<h4>
		<span><?php echo JText::_('COM_NEWSLETTER_LIST_TO_JUSERGROUP_BINDINGS'); ?></span>
		&nbsp;<?php echo JHtml::_('migurhelp.link', 'list/jgroups'); ?>
	</h4>
	<div class="clr"></div>

	<?php if (empty($this->list->list_id)) { ?>
		<span class="badge badge-warning">
			<?php echo JText::_('COM_NEWSLETTER_LIST_TO_JUSERGROUP_BINDINGS_SAVE_FIRST'); ?>
		</span>
	<?php } else { ?>

	<div id="jgroups-pane">
	<table class="adminlist table table-striped" width="100%">

		<thead>
			<tr>
				<th><?php echo JText::_('COM_NEWSLETTER_EVENT_JUSER'); ?></th>
				<th><?php echo JText::_('COM_NEWSLETTER_JUSERGROUP'); ?></th>
				<th><?php echo JText::_('COM_NEWSLETTER_LIST_ACTION'); ?></th>
				<th></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($this->events as $i => $item) : ?>

				<tr>
					<td width="30%">
						<?php echo $this->escape(JText::_('COM_NEWSLETTER_LIST_EVENT_' . strtoupper($item->event))); ?>
					</td>

					<td>
						<?php echo !empty($item->title)? $this->escape($item->title) : '---'; ?>
					</td>

					<td width="30%">
						<?php echo $this->escape(JText::_('COM_NEWSLETTER_LIST_ACTION_' . strtoupper($item->action))); ?>
					</td>

					<td width="110px" align="center">
						<a
							class="modal badge badge-info"
							rel="{handler: 'iframe', size: {x: 400, y: 190}, onClose: function() {}}"
							href="<?php echo JRoute::_("index.php?option=com_newsletter&task=listevent.edit&tmpl=component&le_id=" . (int) $item->le_id . "&list_id=" . (int) $this->list->list_id, false); ?>"
							onclick="Cookie.write('migur-tab-active', '.tab-advanced')"
						>
							<?php echo JText::_('COM_NEWSLETTER_EDIT'); ?>
						</a>

						&nbsp;&nbsp;&nbsp;&nbsp;
						<?php $url =
							JRoute::_("index.php?option=com_newsletter&task=listevent.delete&tmpl=component&", false) .
							"le_id=" . (int) $item->le_id .
							"&" . JSession::getFormToken() . "=1" .
							"&returnUrl=" . urlencode(base64_encode($_SERVER['REQUEST_URI'] . '&activetab=5'));
						?>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="<?php echo $url; ?>" class="badge badge-important">
							<?php echo JText::_('JTOOLBAR_DELETE'); ?>
						</a>
					</td>
				</tr>

			<?php endforeach; ?>

		</tbody>
	</table>
	</div>

	<br/>

	<div style="text-align: right;">
		<a
			class="modal badge badge-info"
			rel="{handler: 'iframe', size: {x: 400, y: 190}, onClose: function() {}}"
			href="<?php echo JRoute::_('index.php?option=com_newsletter&task=listevent.add&tmpl=component&list_id=' . (int) $this->list->list_id, false); ?>"
			onclick="Cookie.write('migur-tab-active', '.tab-advanced')"
		>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('JTOOLBAR_NEW'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</a>
	</div>

	<!--		<div id="list-events-pane">
				<h3><?php echo JText::_('COM_NEWSLETTER_LISTS_EVENTS'); ?></h3>
				<table class="adminlist  table table-striped">
					<thead>
						<tr>
							<th><?php echo JText::_('COM_NEWSLETTER_EVENT'); ?></th>
							<th><?php echo JText::_('COM_NEWSLETTER_GROUP'); ?></th>
							<th><?php echo JText::_('COM_NEWSLETTER_ACTION'); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody data-role="items-list" class="items-list">
					<tbody>
					<tfoot>
						<tr data-role="item-template" class="hide">
							<td>
								<input data-value="" data-type="le_id" type="hidden" />
								<span data-value="" data-type="event"></span>
							</td>
							<td><span data-value="" data-type="group_id"></span></td>
							<td><span data-value="" data-type="action"></span></td>
							<td>
								<a href="#" data-role="item-edit">Edit</a>
								<a href="#" data-role="item-delete">Delete</a>
							</td>
						</tr>
						<tr data-role="item-manage-pane">
							<td>
								<input type="text" data-type="event" />
								<input type="hidden" data-type="le_id" />
							</td>
							<td><input type="text" data-type="group_id" /></td>
							<td><input type="text" data-type="action" /></td>
							<td>
								<a href="#" data-role="item-add" class="btn">Add</a>
								<a href="#" data-role="item-apply" class="btn">Apply</a>
								<a href="#" data-role="item-cancel" class="btn ">Cancel</a>
							</td>
	<?php //JFormHelper::loadFieldClass('juserevents'); ?>
						</tr>
					<tfoot>
				</table>
				<input type="button" data-role="item-new" value="New Item" />
			</div>	-->
	<?php } ?>
</div>

