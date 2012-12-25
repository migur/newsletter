<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

// no direct access
defined('_JEXEC') or die;
?>
<script type="text/javascript">
	Joomla.submitbuttonInstall = function(pressbutton) {
		var form = document.getElementById('installForm');

		// do field validation
		if (form.install_package.value == ""){
			alert("<?php echo JText::_('COM_NEWSLETTER_MSG_INSTALL_PLEASE_SELECT_A_PACKAGE', true); ?>");
		} else {
			form.installtype.value = 'upload';
			form.submit();
		}
	}
</script>

<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=install');?>" method="post" name="installForm" id="installForm">

	<div class="width-70 fltlft">
		<fieldset class="uploadform">
			<legend><?php echo JText::_('COM_NEWSLETTER_UPLOAD_PACKAGE_FILE'); ?></legend>
			<label for="install_package"><?php echo JText::_('COM_NEWSLETTER_PACKAGE_FILE'); ?></label>
			<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
			<input class="button" type="button" value="<?php echo JText::_('COM_NEWSLETTER_UPLOAD_AND_INSTALL'); ?>" onclick="Joomla.submitbuttonInstall('install.install', this.form)" />
		</fieldset>
		<input type="hidden" name="installtype" value="upload" />
		<input type="hidden" name="task" value="install.install" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
