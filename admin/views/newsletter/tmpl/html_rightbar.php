<div id="acc-newsletter">

	<div id="trashcan-container" class="drop trashcan">
		<span><?php echo JText::_('COM_NEWSLETTER_TRASH_MODULES_HERE'); ?></span>
	</div>
	
    <select id="templates-container">
            <option value=""><?php echo JText::_('COM_NEWSLETTER_SELECT_TEMPLATE');?></option>
            <?php foreach($this->templates->items as $item) : ?>
                <option value="<?php echo $this->escape($item->t_style_id); ?>">
                        <?php echo $this->escape($item->title); ?>
                </option>
             <?php endforeach; ?>

    </select>


	<div class="accordion pane-sliders" id="acc-extensions" style="display: block;">
		
		<div class="accordion-group panel">
			
			<div class="accordion-heading" id="acc-modules-native">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#acc-extensions" href="#collapse-native-modules">
					<?php echo JText::_('COM_NEWSLETTER_JOOMLA_MODULES'); ?>
				</a>
			</div>
			
			<div id="collapse-native-modules" class="accordion-body collapse in" >
				<div class="accordion-inner html-slider-modules pane-container container-draggables">
	                <?php foreach($this->modules as $item) :
						if ($item->native == '1') : ?>
	                    <div class="drag module widget" id="<?php echo $this->escape($item->extension); ?>">
							<div class="widget-header"><span><?php echo $this->escape($item->title); ?></span>
								<?php $href = JRoute::_('index.php?option=com_newsletter') . '&view=extension&layout=edit&tmpl=component&extension_id=' . $this->escape($item->extension_id) . '&native=' . $this->escape($item->native); ?>
								<a class="settings icon-pencil" href="<?php echo $href ?>"></a>
							</div>
							<div class="widget-content"></div>
                        </div>
	                <?php endif; endforeach; ?>
				</div>
			</div>
			
		</div>
		
		
		<div class="accordion-group panel">
			
			<div class="accordion-heading" id="acc-modules">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#acc-extensions" href="#collapse-cmp-modules">
					<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MODULES'); ?>
				</a>
			</div>
			
			<div id="collapse-cmp-modules" class="accordion-body collapse">
				<div class="accordion-inner html-slider-modules pane-container container-draggables">

	                <?php foreach($this->modules as $item) :
						if ($item->native == '0') : ?>
	                    <div class="drag module widget" id="<?php echo $this->escape($item->extension); ?>">
							<div class="widget-header"><span><?php echo $this->escape($item->title); ?></span>
								<?php $href = JRoute::_('index.php?option=com_newsletter') . '&view=extension&layout=edit&tmpl=component&type=module&extension_id=' . $this->escape($item->extension_id) . '&native=' . $this->escape($item->native); ?>
								<a class="settings icon-pencil" href="<?php echo $href ?>"></a>
							</div>
							<div class="widget-content"></div>
                        </div>
	                <?php endif; endforeach; ?>

				</div>
			</div>
		</div>
		

		<div class="accordion-group panel">
			
			<div class="accordion-heading" id="acc-plugins">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#acc-extensions" href="#collapse-cmp-plugins">
					<?php echo JText::_('COM_NEWSLETTER_PLUGINS'); ?>
				</a>
			</div>
			
			<div id="collapse-cmp-plugins" class="accordion-body collapse">
				<div class="accordion-inner html-slider-plugins pane-container container-draggables">

                    <?php foreach($this->plugins as $item) : ?>
                        <div class="plugin widget" id="<?php echo $this->escape($item->extension); ?>">
                            <div class="widget-header">
								<span><?php echo $this->escape($item->title); ?></span>
								<a class="settings icon-pencil" href="<?php echo JRoute::_('index.php?option=com_newsletter') . '&view=extension&layout=edit&tmpl=component&type=plugin&extension_id=' . $this->escape($item->extension_id) . '&native=' . $this->escape($item->native); ?>"></a>
	                            <input type="checkbox" value="on">
							</div>
                        </div>
                    <?php endforeach; ?>

				</div>
			</div>
		</div>
		
	</div>

</div>
