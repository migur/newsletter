<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset class="automailing-new">
<legend><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING'); ?></legend>	
<table id="series-list-container" style="width:100%">
    <tr>
		<td width="45%" style="vertical-align: top;" colspan="2">

			<form name="automailingForm" method="POST" id="form-automailing" class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">

				<div id="form-container">
                    <div class="pull-left">
						<?php echo $this->form->getLabel('automailing_name'); ?>
						<?php echo $this->form->getInput('automailing_name'); ?>
                    </div>    
                    <div class="pull-left offset1">
						<?php echo $this->form->getLabel('automailing_event'); ?>
						<?php echo $this->form->getInput('automailing_event'); ?>
                    </div>    
				</div>	
				
				<input type="hidden" id="jf_scope" name="jform[scope]" value="<?php echo  ($this->automailing->automailing_type == "scheduled")? 'targets' : $this->form->getField('scope')->value; ?>">
					
				<input type="hidden" name="task" value="" />
				<?php echo $this->form->getInput('automailing_type'); ?>
				<?php echo $this->form->getInput('automailing_id'); ?>
				<input type="hidden" name="automailing_id" value="<?php echo $this->form->getValue('automailing_id'); ?>" />
				<?php echo JHtml::_('form.token'); ?>
            </form>
				
		</td>
	</tr>			
	<tr>
		<td width="50%">
			<fieldset>
				<legend><?php echo JText::_('COM_NEWSLETTER_SERIES'); ?></legend>	
				<div id="series-container">
					<?php 
						$type = ($this->automailing->automailing_type == "scheduled")?
							'series-scheduled' : 'series-eventbased';
						echo $this->loadTemplate($type, ''); 
					?>
				</div>	
			</fieldset>
		</td>
		<td>			
			<fieldset>
				<legend><?php echo JText::_('COM_NEWSLETTER_LISTS'); ?></legend>
				<div id="lists-container">
					<?php 
						$type = ($this->automailing->automailing_type == "scheduled")?
							'targets-scheduled' : 'targets-eventbased';
						echo $this->loadTemplate($type, ''); 
					?>
				</div>	
			</fieldset>
		</td>
	</tr>
</table>

</fieldset>
