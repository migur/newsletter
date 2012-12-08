<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen');

$showFull = AclHelper::canConfigureComponent(); 

?>

<fieldset id="config-extensions" <?php if (!$showFull) { ?> style="width:98%" <?php } ?>>
	
	<div style="float:right;margin:5px 15px 0 0">
		<a href="<?php echo JRoute::_('index.php?option=com_newsletter&view=install', false); ?>"><?php echo JText::_('COM_NEWSLETTER_MANAGE_EXTENSIONS'); ?></a>
	</div>
	
	<legend><?php echo JText::_('COM_NEWSLETTER_INSTALLED_EXTESIONS'); ?></legend>
	
	<ul id="tabs-extensions" class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#tabext-modules"><?php echo JText::_('COM_NEWSLETTER_MODULES'); ?></a></li>	
		<li><a data-toggle="tab" href="#tabext-plugins"><?php echo JText::_('COM_NEWSLETTER_PLUGINS'); ?></a></li>	
		<li><a data-toggle="tab" href="#tabext-templates"><?php echo JText::_('COM_NEWSLETTER_TEMPLATES'); ?></a></li>	
	</ul>
	
	<div class="tab-content">
		
		<div id="tabext-modules" class="tab-pane active">
			<?php echo $this->loadTemplate('modules', 'extensions'); ?></div>	
			
		<div id="tabext-plugins" class="tab-pane">
			<?php echo $this->loadTemplate('plugins', 'extensions'); ?></div>	
		
		<div id="tabext-templates" class="tab-pane">
			<?php echo $this->loadTemplate('templates', 'extensions'); ?></div>	
	</div>	
	
</fieldset>

<?php if ($showFull) { ?>
<fieldset id="config-config">
	<legend><?php echo JText::_('COM_NEWSLETTER_GLOBAL_CONFIG'); ?></legend>
	
	<form id="adminForm" name="adminForm" method="POST" class="form-horizontal form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">
	
		<ul id="tabs-config" class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#tabconf-general"><?php echo JText::_('COM_NEWSLETTER_GENERAL'); ?></a></li>	
			<li><a data-toggle="tab" href="#tabconf-newsletters"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTERS'); ?></a></li>	
			<li><a data-toggle="tab" href="#tabconf-templates"><?php echo JText::_('COM_NEWSLETTER_TEMPLATES'); ?></a></li>	
			<li><a data-toggle="tab" href="#tabconf-subscribers"><?php echo JText::_('COM_NEWSLETTER_SUBSCRIBERS'); ?></a></li>	
			<li><a data-toggle="tab" href="#tabconf-advanced"><?php echo JText::_('COM_NEWSLETTER_ADVANCED'); ?></a></li>	
			<li><a data-toggle="tab" href="#tabconf-export"><?php echo JText::_('COM_NEWSLETTER_IMPORT_EXPORT'); ?></a></li>	
			<li><a data-toggle="tab" href="#tabconf-permissions"><?php echo JText::_('COM_NEWSLETTER_PERMISSIONS'); ?></a></li>	
		</ul>

		<div class="tab-content">

			<div id="tabconf-general" class="tab-pane active">
				<?php echo $this->loadTemplate('general', 'config'); ?></div>	

			<div id="tabconf-newsletters" class="tab-pane">
				<?php echo $this->loadTemplate('newsletters', 'config'); ?></div>	

			<div id="tabconf-templates" class="tab-pane">
				<?php echo $this->loadTemplate('templates', 'config'); ?></div>	

			<div id="tabconf-subscribers" class="tab-pane">
				<?php echo $this->loadTemplate('subscribers', 'config'); ?></div>	

			<div id="tabconf-advanced" class="tab-pane">
				<?php echo $this->loadTemplate('advanced', 'config'); ?></div>	

			<div id="tabconf-export" class="tab-pane">
				<?php echo $this->loadTemplate('export', 'config'); ?></div>	

			<div id="tabconf-permissions" class="tab-pane">

				<?php
				// First check if user has access to the component.
				if (AclHelper::canConfigureComponent()) {
					echo $this->loadTemplate('permissions', 'config');
				} else { ?>
					<center>
					<?php echo JText::_('COM_NEWSLETTER_YOU_CANT_CHANGE_COMPONENT_PERMISSIONS'); ?>
					</center>
				<?php }	
				echo JHtml::_('tabs.end');
				?>
				<div>
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="returnurl" value="<?php echo base64_encode(JRoute::_('index.php?option=com_newsletter&view=configuration', false)); ?>" />
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</div>	
		</div>	
	</form>
</fieldset>
<?php } ?>
