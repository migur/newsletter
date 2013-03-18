<div id="tab-container-advanced">

    <div class="control-group">
        <label class="control-label">
            <div class="pull-left"><?php echo JText::_('COM_NEWSLETTER_AUTOCONFIRM_USERS')//echo $this->listForm->getLabel('autoconfirm'); ?></div>
			<?php echo JHtml::_('migurhelp.link', 'subscriber', 'subscription', 'autoconfirm'); ?>
        </label>
        <div class="controls">
			<?php echo $this->listForm->getInput('autoconfirm'); ?>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">
            <?php echo $this->listForm->getLabel('send_at_reg'); ?>
        </label>
        <div class="controls">
			<?php echo $this->listForm->getInput('send_at_reg'); ?>
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label">
    		<?php echo $this->listForm->getLabel('send_at_unsubscribe'); ?>
        </label>
        <div class="controls">
			<?php echo $this->listForm->getInput('send_at_unsubscribe'); ?>
        </div>
    </div>
	
<!--		<input name="jform[events]" value="<?php echo json_encode($this->events); ?>" type="hidden" />-->
	<div class="clr"></div>
	
	<br/><br/>
	<h4>
		<span><?php echo JText::_('COM_NEWSLETTER_LIST_TO_JUSERGROUP_BINDINGS'); ?></span>
		&nbsp;<?php echo JHtml::_('migurhelp.link', 'list', 'jgroups'); ?>
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

					<td class="container-eventlist-rowcontrols" width="150px" align="right">
						
						<a
							data-target="#modal-listevent" 
							data-toggle="migurmodal"
							class="btn btn-small"
							href="<?php echo JRoute::_("index.php?option=com_newsletter&task=listevent.edit&tmpl=component&le_id=" . (int) $item->le_id . "&list_id=" . (int) $this->list->list_id, false); ?>" 
							onclick="Cookie.write('migur-tab-active', '.tab-advanced')"
						>
							<i class="icon-out-2"></i>
							<?php echo JText::_('COM_NEWSLETTER_EDIT'); ?>
						</a>
						
						<?php $url = 
							JRoute::_("index.php?option=com_newsletter&task=listevent.delete&tmpl=component&", false) .
							"le_id=" . (int) $item->le_id . 
							"&" . JSession::getFormToken() . "=1" .
							"&returnUrl=" . urlencode(base64_encode($_SERVER['REQUEST_URI'] . '&activetab=5'));
						?>
						&nbsp;&nbsp;
						<a href="<?php echo $url; ?>" class="btn btn-small btn-danger">
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
		<a id="ctrl-listevent-new"
			data-toggle="migurmodal" 
			data-target="#modal-listevent"
			class="btn btn-small btn-success" 
			href="<?php echo JRoute::_('index.php?option=com_newsletter&task=listevent.add&tmpl=component&list_id=' . (int) $this->list->list_id, false); ?>"
			onclick="Cookie.write('migur-tab-active', '.tab-advanced');"
		>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('JTOOLBAR_NEW'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</a>
	</div>
	<?php } ?>
</div>
