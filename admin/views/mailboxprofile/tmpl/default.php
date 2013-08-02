<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset>
<legend><?php echo JText::_('COM_NEWSLETTER_MAILBOX_PROFILE'); ?></legend>	
    <form id="mailboxprofile-form" class="form-validate" name="mailboxprofileForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&layout=') . $this->getLayout(); ?>" method="post">
        <dl>
        <?php foreach($this->ssForm->getFieldset() as $field) { ?>
            <?php if (!$field->hidden) : ?>
                <dt><?php echo $field->label; ?></dt>
                <dd><?php echo $field->input; ?></dd>
            <?php endif; ?>

        <?php } ?>
        </dl>


        <div class="clr"></div>

        <div class="buttons-container">
                <?php echo JToolBar::getInstance('mailbox-toolbar')->render(); ?>
        </div>


        <div>
                <input type="hidden" name="mailbox_profile_id" value="<?php echo $this->ssForm->getValue('mailbox_profile_id'); ?>" />
                <input type="hidden" name="task" value="" />
                <?php echo JHtml::_('form.token'); ?>
        </div>

    </form>
</fieldset>