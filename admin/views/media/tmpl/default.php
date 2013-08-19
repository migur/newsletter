<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>
<form action="index.php?option=com_media&amp;asset=<?php echo JRequest::getCmd('asset');?>&amp;author=<?php echo JRequest::getCmd('author');?>" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' =>22, 'height' => 12), true)?>
	</div>
	<fieldset>
		<div class="pull-left"><span style="line-height:32px;"><?php echo JText::_('COM_NEWSLETTER_MEDIA_INSERT_DESC') ?></span></div>
		<div class="fltrt">
			<button type="button" id="insert-button"><?php echo JText::_('COM_NEWSLETTER_MEDIA_INSERT') ?></button>
			<button type="button" onclick="window.parent.SqueezeBox.close(); return false;"><?php echo JText::_('JCANCEL') ?></button>
		</div>
	</fieldset>

	<iframe
		id="imageframe"
		name="imageframe"
		src="index.php?option=com_media&amp;view=media&amp;tmpl=component&amp;folder=&amp;asset=&amp;author="
		width="800px"
		height="530px"
		frameBorder="0"
	/>

</form>

<stylesheet>
	#imageframe {
		border: 0;
		overflow: hidden;
	}
</stylesheet>
