<fieldset>
	<legend><?php echo JText::_('Statistics'); ?></legend>
	<?php
	jimport('joomla.html.pane');
//1st Parameter: Specify 'tabs' as appearance 
//2nd Parameter: Starting with third tab as the default (zero based index)
//open one!
	$pane = & JPane::getInstance('tabs');
	echo $pane->startPane('pane');
	echo $pane->startPanel('Overview', 'panel_overview');
	echo $this->loadTemplate('overview');
	echo $pane->endPanel();
	echo $pane->startPanel('Hard facts', 'panel_hardfacts');
	echo $this->loadTemplate('hardfacts');
	echo $pane->endPanel();
	echo $pane->endPane();
	?>
</fieldset>