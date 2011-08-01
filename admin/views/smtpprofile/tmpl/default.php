<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset>
    <form id="smtpprofile-form" class="form-validate" name="smtpprofileForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&layout=') . $this->getLayout(); ?>" method="post">
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
                <?php echo JToolBar::getInstance('smtp-toolbar')->render(); ?>
        </div>


        <div>
                <input type="hidden" name="smtp_profile_id" value="<?php echo $this->ssForm->getValue('smtp_profile_id'); ?>" />
                <input type="hidden" name="task" value="" />
                <?php echo JHtml::_('form.token'); ?>
        </div>

    </form>
</fieldset>