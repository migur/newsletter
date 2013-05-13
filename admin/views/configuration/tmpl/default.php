<?php
// no direct access
defined('_JEXEC') or die;

$showFull = AclHelper::canConfigureComponent(); 

?>

<fieldset id="config-extensions" <?php if (!$showFull) { ?> style="width:98%" <?php } ?>>
	
	<div style="float:right;margin:5px 15px 0 0">
		<a href="<?php echo JRoute::_('index.php?option=com_newsletter&view=install', false); ?>"><?php echo JText::_('COM_NEWSLETTER_MANAGE_EXTENSIONS'); ?></a>
	</div>
	
	<legend><?php echo JText::_('COM_NEWSLETTER_INSTALLED_EXTESIONS'); ?></legend>
	<?php
	echo JHtml::_('tabs.start', 'tabs-extensions');
	echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_MODULES'), 'tab-modules');
	echo $this->loadTemplate('modules', 'extensions');
	echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_PLUGINS'), 'tab-plugins');
	echo $this->loadTemplate('plugins', 'extensions');
	echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_TEMPLATES'), 'tab-templates');
	echo $this->loadTemplate('templates', 'extensions');
	echo JHtml::_('tabs.end');
	?>
</fieldset>

<?php if ($showFull) { ?>
<fieldset id="config-config">
	<legend><?php echo JText::_('COM_NEWSLETTER_GLOBAL_CONFIG'); ?></legend>
	<form name="adminForm" method="POST" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">
		<?php
		echo JHtml::_('tabs.start', 'tabs-config');
		echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_GENERAL'), 'tab-general');
		echo $this->loadTemplate('general', 'config');
		echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_NEWSLETTERS'), 'tab-newsletters');
		echo $this->loadTemplate('newsletters', 'config');
		echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_TEMPLATES'), 'tab-templates');
		echo $this->loadTemplate('templates', 'config');
		echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_SUBSCRIBERS'), 'tab-subscribers');
		echo $this->loadTemplate('subscribers', 'config');
		echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_ADVANCED'), 'tab-advanced');
		echo $this->loadTemplate('advanced', 'config');
		echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_IMPORT_EXPORT'), 'tab-export');
		echo $this->loadTemplate('export', 'config');
		echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_PERMISSIONS'), 'tab-permissions');
		?>
		
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
		
		<input type="hidden" name="jform[dryrun_mailing]" value="<?php echo $this->form->getValue('dryrun_mailing'); ?>" />

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="returnurl" value="<?php echo base64_encode(JRoute::_('index.php?option=com_newsletter&view=configuration', false)); ?>" />
		<?php echo JHtml::_('form.token'); ?>
		
	</form>
</fieldset>
<?php } ?>
