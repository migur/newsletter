<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset>
<legend><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SENDING'); ?></legend>

	<div class="fltrt">
		<?php echo JToolBar::getInstance('sender')->render(); ?>
	</div>	

	<div id="send-msg-container"class="fltrt">
		<div id="send-message" class="pull-left"></div>
		<div class="pull-left">&nbsp;&nbsp;&nbsp;</div>
		<div id="send-preloader" class="pull-left"></div>
	</div>

	<table class="nl-subscribers">
            <tr>
                <td width="40%" style="vertical-align: top;"><?php echo $this->loadTemplate('newsletters'); ?></td>
		<td style="vertical-align: top;"><?php echo $this->loadTemplate('lists'); ?></td>
            </tr>
	</table>
</fieldset>
