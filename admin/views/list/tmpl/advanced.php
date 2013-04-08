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

	<?php if (empty($this->list->list_id)) { ?>
		<span class="badge badge-warning">
			<?php echo JText::_('COM_NEWSLETTER_LIST_TO_JUSERGROUP_BINDINGS_SAVE_FIRST'); ?>
		</span>	
	<?php } else { ?>
	
	<div id="jgroups-pane">
		<table id="eventslist-container" class="adminlist table table-striped" width="100%">

			<thead>
				<tr>
					<th><?php echo JText::_('COM_NEWSLETTER_EVENT_JUSER'); ?></th>
					<th><?php echo JText::_('COM_NEWSLETTER_JUSERGROUP'); ?></th>
					<th><?php echo JText::_('COM_NEWSLETTER_LIST_ACTION'); ?></th>
					<th></th>
				</tr>
			</thead>	

			<tbody>
				<!-- Body will be filled up with eventswidget.js manager -->
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
		>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('JTOOLBAR_NEW'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</a>
	</div>
	<?php } ?>
</div>
