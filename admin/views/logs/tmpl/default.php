
<div id="modal-details" class="modal hide fade">
	<div class="modal-header">
		<button data-dismiss="modal" class="close" type="button">x</button>
		<h3><?php echo JText::_('COM_NEWSLETTER_LOG'); ?></h3>
	</div>
	<div class="modal-body"></div>
</div>

<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=logs');?>" method="post" name="adminForm" >

	<?php echo JHtml::_('layout.wrapper'); ?>
	
	<div class="nofloat">
		<div class="pull-left">
			<div class="btn-group pull-left">
				<input class="migur-search" type="text" name="filter_search" id="filter_search" class="filter-search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_NEWSLETTER_FILTER_SEARCH_DESC'); ?>" />
			</div>	
			<div class="btn-group pull-left">
				<button type="submit" class="btn migur-search-submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="btn" onclick="document.id('filter_search').value='';document.id('filter_priority').value='';document.id('filter_category').value='';document.id('filter_dateFrom').value='';document.id('filter_dateTo').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>	
		</div>	
		<div class="pull-left">
			<div class="btn-group pull-left">
				<label class="pull-left"><?php echo JText::_('COM_NEWSLETTER_DATE_FROM'); ?></label>
				<div class="pull-left">
					<?php echo JHtml::_('calendar', $this->state->get('filter.dateFrom'), 'filter_dateFrom', 'filter_dateFrom', '%Y-%m-%d', array('onchange' => 'this.form.submit()')); ?>
				</div>	
			</div>
			<div class="btn-group pull-left">
				<label class="pull-left"><?php echo JText::_('COM_NEWSLETTER_DATE_TO'); ?></label>
				<div class="pull-left">
					<?php echo JHtml::_('calendar', $this->state->get('filter.dateTo'), 'filter_dateTo', 'filter_dateTo', '%Y-%m-%d', array('onchange' => 'this.form.submit()')); ?>
				</div>	
			</div>	
		</div>	
	</div>

	<div class="logslist-container">
		<table class="logslist adminlist  table table-striped" width="100%">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_MESSAGE', 'l.message', $this->listDirn, $this->listOrder, NULL, 'desc'); ?>
				</th>
				<th width="15%" class="left">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_CATEGORY', 'l.category', $this->listDirn, $this->listOrder, NULL, 'desc'); ?>
				</th>
				<th width="15%" class="left">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_DATE', 'l.date', $this->listDirn, $this->listOrder, NULL, 'asc'); ?>
				</th>
				<th width="15%" class="left">
					<?php echo $this->escape(JText::_('COM_NEWSLETTER_ADDITIONAL_DATA')); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php if(count($this->items) > 0) foreach ($this->items as $i => $item) : ?>

			<tr class="item row<?php echo $i % 2; ?>">

				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->log_id); ?>
				</td>

				<td>
				<?php 
					switch($item->priority) {
						case 1:
						case 2:
						case 4:
						case 8:	
							echo '<span style="color:red">'.$item->message.'</span>'; break; 

						case 16: 
							echo '<span style="color:#888800">'.$item->message.'</span>'; break; 
						case 32: 
						case 64: 
							echo '<span style="color:black">'.$item->message.'</span>'; break;

						case 128:
						default:
							echo '<span style="color:gray">'.$item->message.'</span>'; break;
					}		
				?>
				</td>

				<td>
					<?php echo $this->escape($item->category); ?>
				</td>

				<td>
					<?php echo $this->escape($item->date); ?>
				</td>

				<td>
				<?php if(!empty($item->params)) { 

					$data = json_decode(str_replace('\u0000', '', $item->params)); 
				?>
					<label 
						class="icon-list hasTip control-details" 
						style="width:16px;height:16px;cursor:pointer" 
						title="<?php echo $this->escape(JHtml::_('multigrid.renderObject', $data, 0, 'black', array('maxLength' => 100, 'maxLengthMessage' => 'See full log...'))); ?>">
					</label>	
				<?php }	?>
				</td>

			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	</div>	
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	
	<?php echo JHtml::_('layout.wrapperEnd'); ?>
	
</form>
</fieldset>