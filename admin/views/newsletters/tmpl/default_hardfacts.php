<table width="100%" class="sslist adminlist">
		<thead>
			<tr class="row0">
				<th width="40%">&nbsp;</th>
				<th width="20%" class="left">
					<?php echo JText::_('COM_NEWSLETTER_LAST_7_DAYS');?>
				</th>
				<th width="20%" class="left">
					<?php echo JText::_('COM_NEWSLETTER_LAST_30_DAYS');?>
				</th>
				<th width="20%" class="left">
					<?php echo JText::_('COM_NEWSLETTER_LAST_90_DAYS');?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="row1">
				<td>
					<?php echo JText::_('COM_NEWSLETTER_TOTAL_SUBSCRIBERS');?>
				</td>
				<td><?php echo $this->totalSubs[0]; ?></td>
				<td><?php echo $this->totalSubs[1]; ?></td>
				<td><?php echo $this->totalSubs[2]; ?></td>
			</tr>
			<tr  class="row0">
				<td>
					<?php echo JText::_('COM_NEWSLETTER_NEW_SUBSCRIBERS');?>
				</td>
				<td><?php echo $this->newSubs[0]; ?></td>
				<td><?php echo $this->newSubs[1]; ?></td>
				<td><?php echo $this->newSubs[2]; ?></td>
			</tr>
			<tr class="row1">
				<td>
					<?php echo JText::_('COM_NEWSLETTER_LOST_SUBSCRIBERS');?>
				</td>
				<td><?php echo $this->lostSubs[0]; ?></td>
				<td><?php echo $this->lostSubs[1]; ?></td>
				<td><?php echo $this->lostSubs[2]; ?></td>
			</tr>
			<tr  class="row0">
				<td>
					<?php echo JText::_('COM_NEWSLETTER_ACTIVE_SUBSCRIBERS');?>
				</td>
				<td><?php echo $this->activeSubs[0]; ?></td>
				<td><?php echo $this->activeSubs[1]; ?></td>
				<td><?php echo $this->activeSubs[2]; ?></td>
			</tr>
		</tbody>
	</table>