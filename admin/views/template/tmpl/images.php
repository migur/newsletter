        <div id="tab-container-images">

            <div id="images-top">
				<legend><?php echo JText::_('COM_NEWSLETTER_IMAGE_TOP'); ?></legend>
				<div>
					<?php echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('image_top'), $this->tplForm->getInput('image_top')); ?>
					<?php echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('image_top_alt'), $this->tplForm->getInput('image_top_alt')); ?>
				</div>
				<div class="dimensions">
					<?php echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('image_top_width'), $this->tplForm->getInput('image_top_width')); ?>
					<?php echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('image_top_height'), $this->tplForm->getInput('image_top_height')); ?>
				</div>
            </div>
            <div id="images-bottom">
				<legend><?php echo JText::_('COM_NEWSLETTER_IMAGE_BOTTOM'); ?></legend>
				<div>
					<?php echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('image_bottom'), $this->tplForm->getInput('image_bottom')); ?>
					<?php echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('image_bottom_alt'), $this->tplForm->getInput('image_bottom_alt')); ?>
				</div>
				<div class="dimensions">
					<?php echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('image_bottom_width'), $this->tplForm->getInput('image_bottom_width')); ?>
					<?php echo JHtml::_('layout.controlgroup', $this->tplForm->getLabel('image_bottom_height'), $this->tplForm->getInput('image_bottom_height')); ?>
				</div>
            </div>
        </div>
