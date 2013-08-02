<div id="subscribers-list">
	<table class="sslist adminlist  table table-striped" id="table-subs">
		<thead>
			<tr>
				<th class="left">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_SUBSCRIBERS', 'a.name', $this->lists->listDirn, $this->lists->listOrder, null, null, 'listsForm'); ?>
				</th>
				<th class="left" width="60%">
					<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_EMAIL', 'subscribers', $this->lists->listDirn, $this->lists->listOrder, null, null, 'listsForm'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">
					<?php // echo $this->lists->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->subs as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
				   <?php echo $this->escape($item->name); ?>
				</td>
				<td>
				   <?php echo $this->escape($item->email); ?>
				</td>
		   </tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
