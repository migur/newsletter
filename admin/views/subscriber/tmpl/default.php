<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset>
<legend><?php echo JText::_('COM_NEWSLETTER_NEW_SUBSCRIBER'); ?></legend>
<form id="subscriber-form" class="form-validate" name="subscriberForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&layout=') . $this->getLayout(); ?>" method="post">
    <dl>
        <dt>
            <?php echo $this->ssForm->getLabel('name'); ?>
        </dt>
        <dd>
            <?php echo $this->ssForm->getInput('name'); ?>
        </dd>
		<div class="cls"></div>
        <dt>
            <?php echo $this->ssForm->getLabel('email'); ?>
        </dt>
        <dd>
            <?php echo $this->ssForm->getInput('email'); ?>
        </dd>
		<div class="cls"></div>
        <dt>
            <?php echo $this->ssForm->getLabel('html'); ?>
        </dt>
        <dd>
            <?php echo $this->ssForm->getInput('html'); ?>
        </dd>
    </dl>



    <div class="buttons-container">
            <?php echo MigurToolbar::getInstance('subscriber-toolbar')->render(); ?>
    </div>


    <div>
            <?php echo $this->ssForm->getInput('subscriber_id'); ?>
            <?php echo $this->ssForm->getInput('confirmed'); ?>

            <input type="hidden" name="subscriber_id" value="<?php echo $this->ssForm->getValue('subscriber_id'); ?>" />
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
    </div>

</form>
</fieldset>
