<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset id="tabs">
    <div class="legend"><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_CONFIG'); ?></div>
    <form name="templateForm" method="POST" id="adminForm" class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">

		<ul id="tabs-template" class="nav nav-tabs">
			<li class="active">
				<a data-toggle="tab" href="#tab-params"><?php echo JText::_('COM_NEWSLETTER_PARAMS'); ?></a>
			</li>

			<li>
				<a data-toggle="tab" href="#tab-info"><?php echo JText::_('COM_NEWSLETTER_INFO'); ?></a>
			</li>	
		</ul>

		<div class="tab-content">
			<div id="tab-params" class="tab-pane active">

				<div id="ctrl-title" class="control-group">
					<label for="jform_title" class="control-label"><?php echo JText::_('COM_NEWSLETTER_NAME'); ?></label>
					<div class="controls">
						<?php echo $this->tplForm->getInput('title'); ?>
					</div>	
				</div>

				<div class="accordion pane-sliders" id="slider-template">

					<div class="accordion-group panel">

						<div class="accordion-heading" id="slider-dimensions">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slider-template" href="#collapse-slider-dimensions">
								<?php echo JText::_('COM_NEWSLETTER_DIMENSIONS'); ?>
							</a>
						</div>

						<div id="collapse-slider-dimensions" class="accordion-body collapse" >
							<div class="accordion-inner pane-container">
								<?php echo $this->loadTemplate('dimensions', ''); ?>
							</div>
						</div>
					</div>


					<div class="accordion-group panel">

						<div class="accordion-heading" id="slider-images">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slider-template" href="#collapse-slider-images">
								<?php echo JText::_('COM_NEWSLETTER_IMAGES'); ?>
							</a>
						</div>

						<div id="collapse-slider-images" class="accordion-body collapse" >
							<div class="accordion-inner pane-container">
								<?php echo $this->loadTemplate('images', ''); ?>
							</div>
						</div>
					</div>

					<div class="accordion-group panel">

						<div class="accordion-heading" id="slider-colors">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slider-template" href="#collapse-slider-colors">
								<?php echo JText::_('COM_NEWSLETTER_COLORS'); ?>
							</a>
						</div>

						<div id="collapse-slider-colors" class="accordion-body collapse" >
							<div class="accordion-inner pane-container">
								<?php echo $this->loadTemplate('colors', ''); ?>
							</div>
						</div>
					</div>
				</div>	
			</div>	

			<div id="tab-info" class="tab-pane">
				<?php echo $this->loadTemplate('info', ''); ?>
			</div>	
		</div>	
		
        <?php echo $this->tplForm->getInput('t_style_id'); ?>
        <input type="hidden" name="t_style_id" value="<?php echo $this->tplForm->getValue('t_style_id'); ?>" />
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
		
    </form>
</fieldset>