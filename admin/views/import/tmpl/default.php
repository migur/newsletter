
<form id="importForm" class="form-horizontal" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&task=configuration.import'); ?>" method="POST">

	<div class="control-group">
		<label class="control-label"><?php echo JText::_('COM_NEWSLETTER_SELECT_IMPORT_TYPE'); ?></label>
		<div class="controls">
			<select name="jform-import-type">
				<option value="lists"><?php echo JText::_('COM_NEWSLETTER_LISTS_AND_SUBSCRIBERS'); ?></option>
			</select>
		</div>
	</div>	

	<label><?php echo JText::_('COM_NEWSLETTER_SELECT_COMPONENT') . ':'; ?></label>
	<div id="components-list">
		<?php foreach ($this->components as $i => $item) : ?>
			<div>
				<input
					type="radio"
					value="<?php echo $this->escape($item->type); ?>"
					name="jform-com"
					id="jform_html0"
					class=""
					aria-invalid="false"
					<?php echo (!$item->valid)? ' disabled="disabled" ' : ''; ?>
				>
				<label class="<?php echo (!$item->valid)? 'not-available' : ''; ?>">
					<?php echo $this->escape($item->name); ?>
				</label>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="pull-right">
		<div id="import-status">
			<span id="import-message"></span>&nbsp;&nbsp;&nbsp;
			<div id="import-preloader" class="pull-right"></div>
		</div>
		<input class="btn btn-success" name="submit" type="submit" value="<?php echo JText::_('COM_NEWSLETTER_IMPORT'); ?>" />
	</div>

	<input type="hidden" name="subtask" value="" />
	<input type="hidden" name="limit" value="" />
	<input type="hidden" name="offset" value="" />
	<input type="hidden" name="iterative" value="1" />
	<?php echo JHtml::_('form.token'); ?>

</form>
