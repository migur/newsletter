<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset id="maintainance-pane">
	<legend><?php echo JText::_('COM_NEWSLETTER_MAINTAINANCE'); ?></legend>
	
	
	<div style="margin: 0 10px; overflow: hidden;">
		<div style="float:left;">
		<button id="maintainance-check-start">
			<?php echo JText::_('COM_NEWSLETTER_MAINTAINANCE_START'); ?>
		</button>
		</div>
		<div style="float:right">
			<button id="maintainance-get-report" style="display:none;">
				<?php echo JText::_('COM_NEWSLETTER_GET_REPORT'); ?>
			</button>

			<form name="adminForm" method="post" action="?option=com_newsletter">

				<input type="hidden" name="jform[data]" value=""/>
				<input type="hidden" name="task" value="maintainance.getreport"/>

			</form>
		</div>	
		
	</div>
	
	<div id="maintainance-pane-checks">
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
	</div>	
		
</fieldset>
