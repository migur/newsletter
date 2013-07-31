<?php
// no direct access
defined('_JEXEC') or die;
?>

<div id="modal-seriesitem" class="modal hide fade">
	<div class="modal-header">
		<button data-dismiss="modal" class="close" type="button">×</button>
		<h3><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_ITEM'); ?></h3>
	</div>
	<div class="modal-body"></div>
</div>




<div class="automailing-new">
	<div class="legend"><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING'); ?></div>	

	<div id="pane-params">

		<form name="automailingForm" method="POST" id="form-automailing" class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">

			<div id="form-container">
				<?php echo JHtml::_('layout.controlgroup', $this->form->getLabel('automailing_name'), $this->form->getInput('automailing_name')); ?>
				<?php echo JHtml::_('layout.controlgroup', $this->form->getLabel('automailing_event'), $this->form->getInput('automailing_event')); ?>
			</div>	

			<input type="hidden" id="jf_scope" name="jform[scope]" value="<?php echo  ($this->automailing->automailing_type == "scheduled")? 'targets' : $this->form->getField('scope')->value; ?>">

			<input type="hidden" name="task" value="" />
			<?php echo $this->form->getInput('automailing_type'); ?>
			<?php echo $this->form->getInput('automailing_id'); ?>
			<input type="hidden" name="automailing_id" value="<?php echo $this->form->getValue('automailing_id'); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
		 
	</div>				

	<div id="pane-series">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_SERIES'); ?></div>	
		<div id="series-container">
			<?php 
				$type = ($this->automailing->automailing_type == "scheduled")?
					'series-scheduled' : 'series-eventbased';
				echo $this->loadTemplate($type, ''); 
			?>
		</div>	
	</div>	
	<div id="pane-lists">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_LISTS'); ?></div>
		<div id="lists-container">
			<?php 
				$type = ($this->automailing->automailing_type == "scheduled")?
					'targets-scheduled' : 'targets-eventbased';
				echo $this->loadTemplate($type, ''); 
			?>
		</div>	
	</div>
</div>
