<?php
// no direct access
defined('_JEXEC') or die;
?>

	<table class="nl-subscribers">
            <tr>
                <td style="vertical-align: top;"><?php echo $this->loadTemplate('newsletters'); ?></td>
                <td width="1%"></td>
                <td style="vertical-align: top;" id="statistics-container"><?php echo $this->loadTemplate('statistics'); ?></td>
            </tr>
	</table>

