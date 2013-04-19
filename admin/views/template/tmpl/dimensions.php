<div id="tab-container-dimensions">

	<?php if(in_array('width_column1', $this->columns) || in_array('height_column1', $this->columns)) { ?>
	<div id="dimensions1">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_COLUMN') . ' 1'; ?></div>
		<?php 
			if(in_array('width_column1', $this->columns)) {
				echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('width_column1'), $this->tplForm->getInput('width_column1'));
			}

			if(in_array('height_column1', $this->columns)) {
				echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('height_column1'), $this->tplForm->getInput('height_column1'));
			}
		?>
	</div>
	<?php } ?>

	<?php if(in_array('width_column2', $this->columns) || in_array('height_column2', $this->columns)) { ?>
	<div id="dimensions2">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_COLUMN') . ' 2'; ?></div>
		<?php 
			if(in_array('width_column2', $this->columns)) {
				echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('width_column2'), $this->tplForm->getInput('width_column2'));
			}

			if(in_array('height_column2', $this->columns)) {
				echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('height_column2'), $this->tplForm->getInput('height_column2'));
			}
		?>
	</div>
	<?php } ?>

	<?php if(in_array('width_column3', $this->columns) || in_array('height_column3', $this->columns)) { ?>
	<div id="dimensions3">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_COLUMN') . ' 3'; ?></div>
		<?php 
			if(in_array('width_column3', $this->columns)) {
				echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('width_column3'), $this->tplForm->getInput('width_column3'));
			}

			if(in_array('height_column3', $this->columns)) {
				echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('height_column3'), $this->tplForm->getInput('height_column3'));
			}
		?>
	</div>
	<?php } ?>
</div>
