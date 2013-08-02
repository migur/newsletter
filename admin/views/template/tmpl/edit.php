<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset id="tabs">
    <legend><?php echo JText::_('COM_NEWSLETTER_TEMPLATE_CONFIG'); ?></legend>
    <form name="templateForm" method="POST" id="form-template" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">
		
        <?php echo JToolBar::getInstance('multitab-toolbar')->render(); ?>


		<?php
		echo JHtml::_('tabs.start', 'tabs-template');
		echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_PARAMS'), 'tab-params'); ?>

		<div class="form-text info-title">
		<label for="jform_title"><?php echo JText::_('COM_NEWSLETTER_NAME'); ?></label>
		<?php echo $this->tplForm->getInput('title'); ?>
		</div>

		<?php
		echo JHtml::_('sliders.start', 'slider-template');
		echo JHtml::_('sliders.panel', JText::_('COM_NEWSLETTER_DIMENSIONS'), 'slider-dimensions');
		echo $this->loadTemplate('dimensions', '');
		echo JHtml::_('sliders.panel', JText::_('COM_NEWSLETTER_IMAGES'), 'slider-images');
		//echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_IMAGES'), 'tab-images');
		echo $this->loadTemplate('images', '');
		echo JHtml::_('sliders.panel', JText::_('COM_NEWSLETTER_COLORS'), 'slider-colors');
		//echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_COLORS'), 'tab-colors');
		echo $this->loadTemplate('colors', '');
		echo JHtml::_('sliders.end'); ?>

		<?php
		echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_INFO'), 'tab-info');
		echo $this->loadTemplate('info', '');
		echo JHtml::_('tabs.end'); ?>
		
        <?php echo $this->tplForm->getInput('t_style_id'); ?>
        <input type="hidden" name="t_style_id" value="<?php echo $this->tplForm->getValue('t_style_id'); ?>" />
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</fieldset>