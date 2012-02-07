<script>

    var smtpProfiles = <?php echo json_encode($this->smtpprofiles); ?>;

</script>

    <ul id="send" class="form-text">
        <li>
                <?php echo $this->form->getLabel('name'); ?>
                <?php echo $this->form->getInput('name'); ?>
		</li>
		<li>
                <?php echo $this->form->getLabel('subject'); ?>
                <?php echo $this->form->getInput('subject'); ?>
		</li>
		<li>
                <?php echo $this->form->getLabel('encoding', 'params'); ?>
                <?php echo $this->form->getInput('encoding', 'params'); ?>
		</li>
		<li>
                <?php echo $this->form->getLabel('type'); ?>
                <?php echo $this->form->getInput('type'); ?>
		</li>
		<li>
                <?php echo $this->form->getLabel('smtp_profile_id'); ?>

		
                <?php echo $this->form->getInput('smtp_profile_id'); ?>
                <input
                    type="button"
                    name="newsletter_clear_profile"
                    onclick=""
                    class ="button"
                    id="button-newsletter-clear-profile"
                    value="<?php  echo JText::_('COM_NEWSLETTER_CLEAR_PROFILE'); ?>"
                />
		</li>
		<li>
			<div class="fltlft">
                <?php echo $this->form->getLabel('newsletter_from_name', 'params'); ?>
                <?php echo $this->form->getInput('newsletter_from_name', 'params'); ?>
			</div>	
			<div class="fltrt">
                <?php echo $this->form->getLabel('newsletter_to_name', 'params'); ?>
                <?php echo $this->form->getInput('newsletter_to_name', 'params'); ?>
			</div>	
		</li>
		<li>
			<div class="fltlft">
                <?php echo $this->form->getLabel('newsletter_from_email', 'params'); ?>
                <?php echo $this->form->getInput('newsletter_from_email', 'params'); ?>
			</div>	
			<div class="fltrt">
                <?php echo $this->form->getLabel('newsletter_to_email', 'params'); ?>
                <?php echo $this->form->getInput('newsletter_to_email', 'params'); ?>
			</div>	
		</li>	
		<li>
			<label><?php echo JText::_('COM_NEWSLETTER_WEBSITE'); ?></label>
            <div id="link-website-dd">
                <a id="link-website" target="_blank" href="#" rel="index.php?option=com_newsletter&view=show&alias=%s"></a>
                <a id="link-website-prompt" href="#">
                    <?php echo JText::_('COM_NEWSLETTER_START_TO_INPUT'); ?>
                </a>
                <div id="link-website-msg" style="color:gray">
                   ( <?php echo JText::_('COM_NEWSLETTER_LINK_AVAILABLE_AFTER_SAVING'); ?> )
                </div>
            </div>
        </li>
    </ul>
