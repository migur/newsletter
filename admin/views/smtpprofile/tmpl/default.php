<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset>
<legend><?php echo JText::_('COM_NEWSLETTER_SMTP_PROFILE'); ?></legend>	
    <form id="smtpprofile-form" class="form-validate" name="smtpprofileForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&layout=') . $this->getLayout(); ?>" method="post">
        <dl>
			<dt><?php echo $this->ssForm->getLabel('smtp_profile_name'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('smtp_profile_name'); ?></dd>

			<dt><?php echo $this->ssForm->getLabel('from_name'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('from_name'); ?></dd>

			<dt><?php echo $this->ssForm->getLabel('from_email'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('from_email'); ?></dd>

			<dt><?php echo $this->ssForm->getLabel('reply_to_name'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('reply_to_name'); ?></dd>

			<dt><?php echo $this->ssForm->getLabel('reply_to_email'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('reply_to_email'); ?></dd>

			<dt><?php echo $this->ssForm->getLabel('smtp_server'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('smtp_server'); ?></dd>

			<dt><?php echo $this->ssForm->getLabel('smtp_port'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('smtp_port'); ?></dd>

			<dt><?php echo $this->ssForm->getLabel('username'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('username'); ?></dd>

			<dt><?php echo $this->ssForm->getLabel('password'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('password'); ?></dd>

			<dt><?php echo $this->ssForm->getLabel('is_ssl'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('is_ssl'); ?></dd>

			<dt><?php echo $this->ssForm->getLabel('mailbox_profile_id'); ?></dt>
			<dd><?php echo $this->ssForm->getInput('mailbox_profile_id'); ?></dd>
			
			<div class="clr"></div>
			
			<fieldset class="period-conf">
				<legend><?php echo JText::_('COM_NEWSLETTER_MAILING_PERIOD_CONFIGURATION'); ?></legend>

				<p><?php echo $this->ssForm->getLabel('sentsPerPeriodLimit', 'params'); ?></p>
				<dd><?php echo $this->ssForm->getInput('sentsPerPeriodLimit', 'params'); ?></dd>

				<p><?php echo $this->ssForm->getLabel('periodLength', 'params'); ?></p>
				<dd><?php echo $this->ssForm->getInput('periodLength', 'params'); ?></dd>

				<?php echo $this->ssForm->getInput('inProcess', 'params'); ?>
				<?php echo $this->ssForm->getInput('periodStartTime', 'params'); ?>
				<?php echo $this->ssForm->getInput('sentsPerLastPeriod', 'params'); ?>
				
			</fieldset>	
        </dl>

			<?php echo $this->ssForm->getInput('smtp_profile_id'); ?>
        <div class="clr"></div>

        <div class="buttons-container">
                <?php echo JToolBar::getInstance('smtp-toolbar')->render(); ?>
        </div>


        <div>
                <input type="hidden" name="smtp_profile_id" value="<?php echo $this->ssForm->getValue('smtp_profile_id'); ?>" />
                <input type="hidden" name="task" value="" />
                <?php echo JHtml::_('form.token'); ?>
        </div>

    </form>
</fieldset>