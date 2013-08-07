<?php
// no direct access
defined('_JEXEC') or die;
?>

<div>
	<form name="adminForm" method="POST" class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">

		<?php
		echo JHtml::_('layout.controlgroup', $this->form->getLabel('newsletter_id'), $this->form->getInput('newsletter_id'));

		if ($this->automailing->automailing_type == 'eventbased') {

			// This is event based automailing
			if (empty($this->allItems[0]->series_id) || $this->allItems[0]->series_id == $this->seriesId) {

				// And first item. Dont allow to change the date
				echo JHtml::_('layout.controlgroup', $this->form->getLabel('time_start'), '<span class="inputtext-replacer">'.JText::_('COM_NEWSLETTER_FROM_SUBSCRIPTION_DATE').'</span>');

			} else {
				echo JHtml::_('layout.controlgroup', $this->form->getLabel('time_offset'), $this->form->getInput('time_offset'));
			}

		} else {

			// This is scheduled automailing
			if (empty($this->allItems[0]->series_id) || $this->allItems[0]->series_id == $this->seriesId) {

				// And first item. Show the element to set time_stat
				echo JHtml::_('layout.controlgroup', $this->form->getLabel('time_start'), $this->form->getInput('time_start'));

			} else {

				// And first item. Show the element to set time_stat
				echo JHtml::_('layout.controlgroup', $this->form->getLabel('time_offset'), $this->form->getInput('time_offset'));
			}
		}
		?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="jform[automailing_id]" value="<?php echo $this->automailingId; ?>" />
		<input type="hidden" name="series_id" value="<?php echo $this->seriesId; ?>" />

		<?php echo $this->form->getInput('series_id'); ?>
		<?php echo JHtml::_('form.token'); ?>

		<div class="form-actions">
			<?php echo MigurToolbar::getInstance('amitem')->render(); ?>
		</div>

	</form>

</div>
