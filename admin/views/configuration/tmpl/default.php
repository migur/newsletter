<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset id="config-extensions">
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
		echo JHtml::_('tabs.end');
		?>
		<div>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</fieldset>
<?php //var_dump($_SESSION); ?>