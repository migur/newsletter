
	<table>
		<tr>
			<td>
				<span id="am-name-label"><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_NAME') . ":"; ?>&nbsp;&nbsp;</span>
			</td>
			<td>
				<span id="am-name"><b><?php echo $this->escape($this->automailingItems->items[0]->automailing_name); ?></b></span>
			</td>	
		</tr>	
		<tr>
			<td>
				<span id="am-email-label"><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_TYPE') . ":"; ?>&nbsp;&nbsp;</span>
			</td>
			<td>
				<span id="am-email"><b><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_'.strtoupper($this->automailingItems->items[0]->automailing_type)); ?></b></span>
			</td>
		</tr>	
		<tr>
			<td>
				<span id="am-targets-label"><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_TARGETS') . ":"; ?>&nbsp;&nbsp;</span>
			</td>
			<td>
				<div id="am-targets">
					<?php
						foreach($this->automailingTargets as &$list) {
							foreach($list as &$name) { ?>
								<span><?php echo $this->escape($name); ?></span>
					<?php }} ?>
			</td>
		</tr>	
	</table>	
		
	<form id="form-automailing" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=automailing&layuot=details');?>" method="post" name="automailingForm" >
		
		<table class="automailingitems adminlist" width="100%">
			<thead>
				<tr>
					<th class="left" width="35%">
							<?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_DATE'); ?>
					</th>
					<th class="left" width="35%">
							<?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_NEWSLETTER_NAME'); ?>
					</th>
					<th class="left" width="20%">
							<?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_STATUS'); ?>
					</th>
					<th class="left" width="10%">
							<?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_SENT'); ?>
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
			<?php
				foreach ($this->automailingItems->items as $i => $item) {
			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php echo $this->escape($item->time_verbal); ?>
					</td>
					<td>
						<?php echo $this->escape($item->newsletter_name); ?>
					</td>
					<td>
						<?php echo $this->escape($item->status); ?>
					</td>
					<td>
						<?php echo $this->escape($item->sent); ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $this->automailings->listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->automailings->listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
