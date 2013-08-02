<?php
// no direct access
defined('_JEXEC') or die;
?>

<div style="margin: 0 10px; overflow: hidden;">
	<div style="float:left;">
	<button id="maintainance-check-start" class="btn">
		<?php echo JText::_('COM_NEWSLETTER_MAINTAINANCE_START'); ?>
	</button>
	</div>
	<div style="float:right">
		<button id="maintainance-get-report"  class="btn btn-success" style="display:none;">
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
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_ENVIRONMENT_CHECK'); ?></div>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button id="environment-check-start" class="btn refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>


	<fieldset id="db-check-pane" class="check-pane">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_DB_CHECK'); ?></div>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="btn refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>


	<fieldset id="smtp-check-pane" class="check-pane">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_SMTP_CHECK'); ?></div>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="btn refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>


	<fieldset id="mailbox-check-pane" class="check-pane">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_MAILBOX_CHECK'); ?></div>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="btn refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>

	<fieldset id="license-check-pane" class="check-pane">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_LICENSE_CHECK'); ?></div>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="btn refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>
	<fieldset id="extensions-check-pane" class="check-pane">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_MAINTAINANCE_CHECKEXTENSIONS'); ?></div>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="btn refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>
	<fieldset id="generalsysinfo-check-pane" class="check-pane">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_GENERALSYSINFO_CHECK'); ?></div>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="btn refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>
	<fieldset id="jdirectories-check-pane" class="check-pane">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_JDIRECTORIES_CHECK'); ?></div>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="btn refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>
	<fieldset id="phpsettings-check-pane" class="check-pane">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_PHPSETTINGS_CHECK'); ?></div>
		<div class="status">
			<span class="status-verbal"></span>		
			<span class="preloader-container"></span>
			<button class="btn refresh-control" style="float:right;">
				<?php echo JText::_('COM_NEWSLETTER_REFRESH'); ?>
			</button>
		</div>
		<div class="notifications"></div>
		<div class="suggestions"></div>
	</fieldset>
</div>	
