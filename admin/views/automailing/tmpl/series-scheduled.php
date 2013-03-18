<form 
	method="POST" 
	name="automailingitemsForm" 
	id="automailingitemsForm" 
	action="<?php echo JRoute::_('index.php?option=com_newsletter&view=automailing&layout='.$this->getLayout().'&automailing_id='.$this->automailingId, false); ?>"
>
	<div class="pull-right">
	<?php 
		if (!$this->automailing->finished) { ?>
			<a
				class="ctrl-seriesitem-new btn btn-success" 
				data-toggle="migurmodal" 
				data-target="#modal-seriesitem"
				href="<?php echo JRoute::_('index.php?option=com_newsletter&view=automailingitem&layout=edit&tmpl=component&automailing_id=' . (int) $this->automailingId, false); ?>"
			>
				<?php echo JText::_('JTOOLBAR_NEW'); ?>
			</a>
		<?php } else { ?>
			<span><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_FINISHED'); ?></span>
			<br/>&nbsp;
		<?php } ?>
	</div>	
	
	<?php 
	foreach($this->automailingItems->items as $idx => $serie) { 
		$editable = ($serie->status == 0 || $this->automailing->automailing_type == 'eventbased');
	?>
	<div class="<?php echo ($editable)? 'item' : 'item-processed'; ?>">
			
			<?php if ($editable) { ?>
				<a
					class="edit" 
					data-toggle="migurmodal" 
					data-target="#modal-seriesitem"
					href="<?php echo JRoute::_('index.php?option=com_newsletter&task=automailingitem.edit&tmpl=component&series_id=' . (int) $serie->series_id, false); ?>"
				>
					<?php echo JText::_('COM_NEWSLETTER_EDIT'); ?>
				</a>
				<a href="#" class="close"></a>
			<?php } ?>	
			<div class="date"><?php echo $this->escape($serie->time_verbal); ?></div>
			<div class="name"><?php echo $this->escape($serie->newsletter_name); ?></div>
			<input type="hidden" value="<?php echo $serie->series_id; ?>" name="cid[]" />			
		</div> 
	<?php if ($idx < count($this->automailingItems->items)-1) { ?>
		<div class="arrow"></div>
	<?php }} ?>	
		
    <input type="hidden" name="filter_order" value="<?php echo $this->automailingItems->listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->automailingItems->listDirn; ?>" />
    <input type="hidden" name="automailing_id" value="<?php echo $this->automailingId; ?>" />
    <input type="hidden" name="item_id" value="" />
    <input type="hidden" name="form" value="automailingitems" />
    <input type="hidden" name="task" value="" />

</form>
