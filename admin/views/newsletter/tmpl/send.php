<script>

    var smtpProfiles = <?php echo json_encode($this->smtpprofiles); ?>;

</script>

    <div id="send">

		<div class="container-top">

			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('name'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('name'); ?>
				</div>
			</div>


			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('subject'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('subject'); ?>
				</div>
			</div>


			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('alias'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('alias'); ?>
				</div>
			</div>

		</div>


		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('encoding', 'params'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('encoding', 'params'); ?>
			</div>
		</div>


		<div class="control-group">
			<div class="control-label">
                <?php echo $this->form->getLabel('type'); ?>
			</div>
			<div class="controls">
                <?php echo $this->form->getInput('type'); ?>
			</div>
		</div>


		<div class="control-group">
			<div class="control-label">
                <?php echo $this->form->getLabel('smtp_profile_id'); ?>
				&nbsp;&nbsp;<?php echo JHtml::_('migurhelp.link', 'newsletter/smtp'); ?>


                <?php echo $this->form->getInput('smtp_profile_id'); ?>
                <input
                    type="button"
                    name="newsletter_clear_profile"
                    onclick=""
                    class ="button btn btn-danger"
                    id="button-newsletter-clear-profile"
                    value="<?php  echo JText::_('COM_NEWSLETTER_CLEAR_PROFILE'); ?>"
                />
			</div>
		</div>

		<div class="control-group">
			<div class="control-label">
                <?php echo $this->form->getLabel('newsletter_from_name', 'params'); ?>
			</div>
			<div class="controls">
                <?php echo $this->form->getInput('newsletter_from_name', 'params'); ?>
			</div>
		</div>


		<div class="control-group">
			<div class="control-label">
                <?php echo $this->form->getLabel('newsletter_to_name', 'params'); ?>
			</div>
			<div class="controls">
                <?php echo $this->form->getInput('newsletter_to_name', 'params'); ?>
			</div>
		</div>


		<div class="control-group">
			<div class="control-label">
                <?php echo $this->form->getLabel('newsletter_from_email', 'params'); ?>
			</div>
			<div class="controls">
                <?php echo $this->form->getInput('newsletter_from_email', 'params'); ?>
			</div>
		</div>


		<div class="control-group">
			<div class="control-label">
                <?php echo $this->form->getLabel('newsletter_to_email', 'params'); ?>
			</div>
			<div class="controls">
                <?php echo $this->form->getInput('newsletter_to_email', 'params'); ?>
			</div>
		</div>

		<div class="control-group">
			<div class="control-label">
                <?php echo JText::_('COM_NEWSLETTER_WEBSITE'); ?>
			</div>
			<div id="link-website-dd" class="controls">
				<a id="link-website" target="_blank" href="#" rel="index.php?option=com_newsletter&view=show&alias=%s"></a>
				<a id="link-website-prompt" href="#" onclick="return false;">
					<?php echo JText::_('COM_NEWSLETTER_START_TO_INPUT'); ?>
				</a>
				<div id="link-website-msg" style="color:gray">
				   ( <?php echo JText::_('COM_NEWSLETTER_LINK_AVAILABLE_AFTER_SAVING'); ?> )
				</div>
			</div>
		</div>

    </div>
