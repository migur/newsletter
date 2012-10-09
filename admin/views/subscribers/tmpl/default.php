<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen');
?>

	<table class="nl-subscribers">
            <tr>
                <td width="53%" style="vertical-align: top;"><?php echo $this->loadTemplate('subscribers'); ?></td>
                <td width="1%"></td>
		<td style="vertical-align: top;"><?php echo $this->loadTemplate('lists'); ?></td>
            </tr>
	</table>
