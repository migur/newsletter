<?php $items = $this->form->getFieldset('general'); ?>

<table class="adminlist" width="100%">
        <thead>
            <tr>
                <th width="40%" class="left">
                    <?php echo JText::_('COM_NEWSLETTER_CONFIG_NAME'); ?>
                </th>
                <th width="40%" class="left">
                    <?php echo JText::_('COM_NEWSLETTER_CONFIG_VALUE'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
                <tr class="row<?php echo $i % 2; ?>">
                    <td><?php echo current($items)->label; ?></td>
                    <td><?php echo current($items)->input; ?></td>
                </tr>
                <tr class="row<?php echo $i % 2; ?>">
                    <td><?php next($items); echo current($items)->label; ?></td>
                    <td><?php echo current($items)->input; ?></td>
                </tr>
                <tr class="row<?php echo $i % 2; ?>">
                    <td><?php next($items); echo current($items)->label; ?></td>
                    <td>
					<?php 
						echo current($items)->input;
						next($items); ?>
					<div class="clr"></div>						
					<?php	
						echo current($items)->input; ?>
						<?php next($items); echo current($items)->input; 
						next($items);
						echo current($items)->input; 
					?></td>
					
                </tr>
                <tr class="row<?php echo $i % 2; ?>">
                    <td><?php next($items); echo current($items)->label; ?></td>
                    <td>
					<?php 
						echo current($items)->input;
						next($items); ?>
					<div class="clr"></div>						
					<?php	
						echo current($items)->input; ?>
						<?php next($items); echo current($items)->input; 
						next($items);
						echo current($items)->input; 
					?></td>
					
                </tr>
                <tr class="row<?php echo $i % 2; ?>">
                    <td><?php next($items); echo current($items)->label; ?></td>
                    <td><?php echo current($items)->input; ?></td>
                </tr>
                <tr class="row<?php echo $i % 2; ?>">
                    <td colspan="2">
						<?php next($items); ?>
						<?php echo current($items)->label; ?>
						<?php echo current($items)->input; ?>
						<?php next($items); ?>
						<?php echo current($items)->label; ?>
						<?php echo current($items)->input; ?>
						<?php next($items); ?>
						<?php echo current($items)->label; ?>
						<?php echo current($items)->input; ?>
					</td>
                </tr>
        </tbody>
    </table>
