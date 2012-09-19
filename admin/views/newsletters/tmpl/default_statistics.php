<fieldset>
	<legend><?php echo JText::_('Statistics'); ?></legend>
	
	<ul id="prewiew" class="nav nav-tabs">
		<li class="active">
			<a data-toggle="tab" href="#statistics-overview"><?php echo JText::_('COM_NEWSLETTER_OVERVIEW'); ?></a>
		</li>
		
		<li>
			<a data-toggle="tab" href="#statistics-hardfacts"><?php echo JText::_('COM_NEWSLETTER_HARDFACTS'); ?></a>
		</li>	
	</ul>

	<div class="tab-content">
		<div id="statistics-overview" class="tab-pane active">
			<?php echo $this->loadTemplate('overview'); ?>
		</div>	
		<div id="statistics-hardfacts" class="tab-pane">
			<?php echo $this->loadTemplate('hardfacts'); ?>
		</div>	
	</div>	
	
</fieldset>