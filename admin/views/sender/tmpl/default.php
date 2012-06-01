<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset>
<legend><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SENDING'); ?></legend>

	<?php echo JToolBar::getInstance('sender')->render(); ?>
	<table class="nl-subscribers">
            <tr>
                <td width="40%" style="vertical-align: top;"><?php echo $this->loadTemplate('newsletters'); ?></td>
		<td style="vertical-align: top;"><?php echo $this->loadTemplate('lists'); ?></td>
            </tr>
	</table>
</fieldset>
