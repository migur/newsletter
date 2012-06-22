<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset>
<legend><?php echo JText::_('COM_NEWSLETTER_NEW_SUBSCRIBER'); ?></legend>
<form id="uninstall-form" class="form-validate" name="subscriberForm" action="<?php echo JRoute::_('index.php?option=com_installer') ?>" method="post">

	<h1><span style="color:red">			
		<?php echo JText::_('COM_NEWSLETTER_YOU_ARE_ABOUT_TO_DELETE_COMPONENT'); ?>
	</span></h1>
	
	<h3>
		<?php echo JText::_('COM_NEWSLETTER_UNINSTALL_GO_OR_BACK'); ?>
	</h3>
	
	<br/>
	<br/>
	
	<div>
		<span style="display:block;float:left"><?php echo JText::_('COM_NEWSLETTER_REMOVE_DB_DATA'); ?></span>
		<input type="checkbox" name="com_newsletter_dbremove" style="margin:1px 0 0 5px;"/>
	</div>
	
	<br/>
	<br/>

	<input type="hidden" name="cid[]" value="<?php echo $this->extension['extension_id']; ?>" />
	<input type="hidden" name="task" value="manage.remove" />
	<input type="hidden" name="com_newsletter_uninstall" value="1" />
	<?php echo JHtml::_('form.token'); ?>

	<div>
		
		<input 
			type="button"
			class="btn" 
			style="float:left;"
			onclick="document.location.href='<?php echo JRoute::_('index.php?option=com_installer&view=manage', false); ?>'"
			value="<?php echo JText::_('COM_NEWSLETTER_UNINSTALL_BACK_TO_MANAGER'); ?>"
		/>
		
		<input
			type="submit"
			class="btn" 
			style="float:right;" 
			value="<?php echo JText::_('COM_NEWSLETTER_UNINSTALL_COMPONENT'); ?>"
		/>
		
	</div>
</form>
</fieldset>
