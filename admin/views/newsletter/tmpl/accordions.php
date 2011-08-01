<div id="acc-newsletter">

    <select id="templates-container">
            <option value=""><?php echo JText::_('COM_NEWSLETTER_SELECT_TEMPLATE');?></option>
            <?php foreach($this->templates->items as $item) : ?>
                <option value="<?php echo $this->escape($item->t_style_id); ?>">
                        <?php echo $this->escape($item->title); ?>
                </option>
             <?php endforeach; ?>

    </select>
    <div class="pane-sliders" style="display: block;">

        <div class="panel">
            <h3 id="acc-modules-native" class="pane-toggler title">
                <a href="javascript:void(0);">
                    <span><?php echo JText::_('COM_NEWSLETTER_JOOMLA_MODULES'); ?></span>
                </a>
            </h3>
            <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: 0px;">
                <div class="html-slider-modules pane-container container-draggables">

	                <?php foreach($this->modules as $item) :
						if ($item->native == '1') : ?>
	                    <div class="drag module" id="<?php echo $this->escape($item->extension); ?>">
							<div class="widget-header"><?php echo $this->escape($item->title); ?>
								<?php
									if ($item->extension == 'mod_img') {
										$href = "?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid=jform_params_imageurl&amp;folder=";
									} else {
										$href = JRoute::_('index.php?option=com_newsletter') . '&view=extension&layout=edit&tmpl=component&extension_id=' . $this->escape($item->extension_id) . '&native=' . $this->escape($item->native);
									}
									//  echo $href;
								?>

								<a class="settings icon-16-gear-disabled" href="<?php echo $href ?>"></a>
							</div>
							<div class="widget-content"></div>
                        </div>
	                <?php endif; endforeach; ?>

                </div>
            </div>
        </div>

        <div class="panel">
            <h3 id="acc-modules" class="pane-toggler title">
                <a href="javascript:void(0);">
                    <span><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MODULES'); ?></span>
                </a>
            </h3>
            <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: 0px;">
                <div class="html-slider-modules pane-container container-draggables">

	                <?php foreach($this->modules as $item) :
						if ($item->native == '0') : ?>
	                    <div class="drag module" id="<?php echo $this->escape($item->extension); ?>">
							<div class="widget-header"><?php echo $this->escape($item->title); ?>
								<?php
									if ($item->extension == 'mod_img') {
										$href = "?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid=jform_params_imageurl&amp;folder=";
									} else {
										$href = JRoute::_('index.php?option=com_newsletter') . '&view=extension&layout=edit&tmpl=component&type=module&extension_id=' . $this->escape($item->extension_id) . '&native=' . $this->escape($item->native);
									}
									//  echo $href;
								?>

								<a class="settings icon-16-gear-disabled" href="<?php echo $href ?>"></a>
							</div>
							<div class="widget-content"></div>
                        </div>
	                <?php endif; endforeach; ?>

                </div>
            </div>
        </div>


        <div class="panel">
            <h3 id="acc-plugins" class="pane-toggler title">
                <a href="javascript:void(0);">
                    <span>Plugins</span>
                </a>
            </h3>
            <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: 0px;">
                <div id="html-slider-plugins" class="pane-containe">

                    <?php foreach($this->plugins as $item) : ?>
                        <div class="plugin" id="<?php echo $this->escape($item->extension); ?>">
                            <div class="widget-header">
								<?php echo $this->escape($item->title); ?>
								<a class="settings icon-16-gear" href="<?php echo JRoute::_('index.php?option=com_newsletter') . '&view=extension&layout=edit&tmpl=component&type=plugin&extension_id=' . $this->escape($item->extension_id) . '&native=' . $this->escape($item->native); ?>"></a>
	                            <input type="checkbox" value="on">
							</div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>


    </div>
</div>





<div class="pane-sliders" id="acc2-newsletter" style="display: block;">

    
    <div class="panel">
        <h3 id="acc2-dynamicdata" class="title pane-toggler-down">
            <a href="javascript:void(0);">
                <span>Dynamic Data</span>
            </a>
        </h3>
        <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: auto;">
            <div class="pane-container">
                <div id="dynamic-data-container">
                    <?php foreach ($this->dynamicData as $name => $item) { ?>
                        <div class="data">
                            <a href="<?php echo $item; ?>" rel="<?php echo $item; ?>"><?php echo $name; ?> +</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>


<!--    <div class="panel">
        <h3 id="acc2-plugins" class="pane-toggler title">
            <a href="javascript:void(0);">
                <span>Plugins</span>
            </a>
        </h3>
        <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: 0px;">
            <div class="pane-container"> plugins </div>
        </div>
    </div>-->

    
</div>