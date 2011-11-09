<fieldset id="newsletters-fieldset">
<legend><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER'); ?></legend>
    <select id="newsletter-select">
        <option value=""><?php echo JText::_('Please selct newsletter first'); ?></option>
        <?php foreach($this->newsletters as $item) { ?>
        <option value="<?php echo $this->escape($item->newsletter_id); ?>">
            <?php echo $this->escape($item->name); ?>
        </option>
        <?php } ?>
    </select>
</fieldset>