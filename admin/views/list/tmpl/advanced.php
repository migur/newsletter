    <div id="tab-container-advanced" class="form-text">
        <dl>
            <dt>
				<label>
                <?php echo JText::_('COM_NEWSLETTER_AUTOCONFIRM_USERS')//echo $this->listForm->getLabel('autoconfirm'); ?>
				</label>	
            </dt>
            <dd>
                <?php echo $this->listForm->getInput('autoconfirm'); ?>
            </dd>
            <dt>
                <?php echo $this->listForm->getLabel('send_at_reg'); ?>
            </dt>
            <dd>
                <?php echo $this->listForm->getInput('send_at_reg'); ?>
            </dd>
            <dt>
                <?php echo $this->listForm->getLabel('send_at_unsubscribe'); ?>
            </dt>
            <dd>
                <?php echo $this->listForm->getInput('send_at_unsubscribe'); ?>
            </dd>
        </dl>
    </div>
