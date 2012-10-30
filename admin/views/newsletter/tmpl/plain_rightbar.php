<div class="accordion pane-sliders" id="acc2-newsletter">
    
	<div class="accordion-group panel">

		<div class="accordion-heading" id="acc2-dynamicdata">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#acc2-newsletter" href="#collapse-dynamic-data">
				<?php echo JText::_('COM_NEWSLETTER_DYNAMIC_DATA'); ?>
			</a>
		</div>

		<div id="collapse-dynamic-data" class="accordion-body collapse in">
			<div class="accordion-inner pane-container">
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
</div>