
<table class="adminlist  table table-striped" width="100%">
	<thead>
		<tr>
			<th width="40%" class="left">
				<?php echo JText::_('COM_NEWSLETTER_CONFIG_NAME'); ?>
			</th>
			<th width="40%" class="left">
				<?php echo JText::_('COM_NEWSLETTER_CONFIG_VALUE'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr class="row0">
			<td><?php echo $this->form->getLabel('license_key'); ?></td>
			<td><?php echo $this->form->getInput('license_key'); ?></td>
		</tr>
		<tr class="row0">
			<td><?php echo $this->form->getLabel('fbappid'); ?></td>
			<td><?php echo $this->form->getInput('fbappid'); ?></td>
		</tr>
		<tr class="row0">
			<td><?php echo $this->form->getLabel('fbsecret'); ?></td>
			<td><?php echo $this->form->getInput('fbsecret'); ?></td>
		</tr>
		<tr class="row0">
			<td><?php echo $this->form->getLabel('debug'); ?></td>
			<td><?php echo $this->form->getInput('debug'); ?></td>
		</tr>
		<tr class="row0">
			<td><label><?php echo JText::_('COM_NEWSLETTER_MAINTAINANCE_DESC'); ?></label></td>
			<td>
				<a 
					class="modal"
					rel="{handler: 'iframe', size: {x: 600, y: 650}, onClose: function() {}}" 
					href="<?php echo JRoute::_('index.php?option=com_newsletter&view=maintainance&tmpl=component', true); ?>">
					
					<?php echo JText::_('COM_NEWSLETTER_MAINTAINANCE'); ?>	
				</a>	
			</td>
		</tr>
		<tr class="row0">
			<td><?php echo $this->form->getLabel('rawurls'); ?></td>
			<td><?php echo $this->form->getInput('rawurls'); ?></td>
		</tr>
	</tbody>
</table>

<?php echo $this->form->getInput('monster_url'); ?>
<?php echo $this->form->getInput('product'); ?>
<?php echo $this->form->getInput('domain'); ?>