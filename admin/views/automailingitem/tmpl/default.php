<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset class="automailing-new">
<legend><?php echo JText::_('COM_NEWSLETTER_AUTOMAILING_ITEM'); ?></legend>	


	<form name="adminForm" method="POST" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">

		<?php 
		echo $this->form->getLabel('newsletter_id');
		echo $this->form->getInput('newsletter_id');

		if ($this->automailing->automailing_type == 'eventbased') {

			// This is event based automailing
			if (empty($this->allItems[0]->series_id) || $this->allItems[0]->series_id == $this->seriesId) {

				// And first item. Dont allow to change the date
				echo $this->form->getLabel('time_start');
				echo '<div class="labeled">'.JText::_('COM_NEWSLETTER_FROM_SUBSCRIPTION_DATE').'</div>';

			} else {
				
				echo $this->form->getLabel('time_offset');
				echo $this->form->getInput('time_offset');
			}
			
		} else {

			// This is scheduled automailing
			if (empty($this->allItems[0]->series_id) || $this->allItems[0]->series_id == $this->seriesId) {

				// And first item. Show the element to set time_stat
				echo $this->form->getLabel('time_start');
				echo $this->form->getInput('time_start');

			} else {
				
				// And first item. Show the element to set time_stat
				echo $this->form->getLabel('time_offset');
				echo $this->form->getInput('time_offset');
			}
		}	
		?>
		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="jform[automailing_id]" value="<?php echo $this->automailingId; ?>" />
		<input type="hidden" name="series_id" value="<?php echo $this->seriesId; ?>" />
		
		<?php echo $this->form->getInput('series_id'); ?>
		<?php echo JHtml::_('form.token'); ?>
		
		<div class="clr"></div>
		<?php echo JToolBar::getInstance('amitem')->render(); ?>
		
	</form>

</fieldset>
