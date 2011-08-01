    <div id="tab-container-info">

        <div id="info2" class="form-text">
            <dl>
                <dt>
                    <?php echo JText::_('COM_NEWSLETTER_AUTHOR'); ?>
                </dt>
                <dd>
                    <?php echo $this->escape($this->tplInfo->author); ?>
                </dd>
                <dt>
                    <?php echo JText::_('COM_NEWSLETTER_COPYRIGHT'); ?>
                </dt>
                <dd>
                    <?php echo $this->escape($this->tplInfo->copyright); ?>
                </dd>
                <dt>
                    <?php echo JText::_('COM_NEWSLETTER_VERSION'); ?>
                </dt>
                <dd>
                    <?php echo $this->escape($this->tplInfo->version); ?>
                </dd>
            </dl>
        </div>


        <div id="info3" class="form-text">
            <dl>
                <dt>
                    <?php echo JText::_('COM_NEWSLETTER_AUTHOR_EMAIL'); ?>
                </dt>
                <dd>
                    <?php echo $this->escape($this->tplInfo->authorEmail); ?>
                </dd>
                <dt>
                    <?php echo JText::_('COM_NEWSLETTER_LICENSE'); ?>
                </dt>
                <dd>
                    <?php echo $this->escape($this->tplInfo->license); ?>
                </dd>
                <dt>
                    <?php echo JText::_('COM_NEWSLETTER_CREATION_DATE'); ?>
                </dt>
                <dd>
                    <?php echo $this->escape($this->tplInfo->creationDate); ?>
                </dd>
            </dl>
        </div>


        <div id="info4" class="form-text">
            <dl>
                <dt>
                    <?php echo JText::_('COM_NEWSLETTER_DESCRIPTION'); ?>
                </dt>
                <dd>
                    <?php echo $this->escape($this->tplInfo->description); ?>
                </dd>
            </dl>
        </div>
    </div>
