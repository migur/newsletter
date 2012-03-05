<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset id="maintenance-pane">
	<legend><?php echo JText::_('COM_NEWSLETTER_MAINTENANCE'); ?></legend>
	
	
	<div>
		<button id="environment-check-start" style="float:none;">
			<?php echo JText::_('COM_NEWSLETTER_START'); ?>
		</button>
	</div>
	
	
	<fieldset id="environment-check-pane" class="check-pane">
		<legend><?php echo JText::_('COM_NEWSLETTER_ENVIRONMENT_CHECK'); ?></legend>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button id="environment-check-start" class="refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>

	
	<fieldset id="db-check-pane" class="check-pane">
		<legend><?php echo JText::_('COM_NEWSLETTER_DB_CHECK'); ?></legend>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>
	
	
	<fieldset id="smtp-check-pane" class="check-pane">
		<legend><?php echo JText::_('COM_NEWSLETTER_SMTP_CHECK'); ?></legend>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>
	
	
	<fieldset id="mailbox-check-pane" class="check-pane">
		<legend><?php echo JText::_('COM_NEWSLETTER_MAILBOX_CHECK'); ?></legend>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>

	
</fieldset>
