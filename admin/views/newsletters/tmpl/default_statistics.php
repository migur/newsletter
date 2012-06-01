<fieldset>
	<legend><?php echo JText::_('Statistics'); ?></legend>
	<?php
//1st Parameter: Specify 'tabs' as appearance 
//2nd Parameter: Starting with third tab as the default (zero based index)
//open one!
	echo JHtml::_('tabs.start', 'pane');
	echo JHtml::_('tabs.panel', 'Overview', 'panel_overview');
	echo $this->loadTemplate('overview');
	echo JHtml::_('tabs.panel', 'Hard facts', 'panel_hardfacts');
	echo $this->loadTemplate('hardfacts');
	echo JHtml::_('tabs.end');
	?>
</fieldset>