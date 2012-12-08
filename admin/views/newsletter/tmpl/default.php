<?php
// no direct access
defined('_JEXEC') or die;
?>
<div id="tabs">
    <legend id="tabs-legend"><?php echo JFactory::getDocument()->getTitle(); ?></legend>
	
	<form name="newsletterForm" method="POST" id="tabs-sub-container" class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">

		<ul id="tabs-newsletter" class="nav nav-tabs" data-mnc-role="tab-header-container">
			<li class="active" data-mcn-role="tab-header">
				<a data-toggle="tab" href="#tab-send"><?php echo JText::_('COM_NEWSLETTER_SEND'); ?></a>
			</li>

			<li data-mcn-role="tab-header">
				<a data-toggle="tab" href="#tab-html"><?php echo JText::_('COM_NEWSLETTER_HTML'); ?></a>
			</li>	

			<li data-mcn-role="tab-header">
				<a data-toggle="tab" href="#tab-plain"><?php echo JText::_('COM_NEWSLETTER_PLAIN'); ?></a>
			</li>	

			<li data-mcn-role="tab-header">
				<a data-toggle="tab" href="#tab-attachments"><?php echo JText::_('COM_NEWSLETTER_ATTACHMENTS'); ?></a>
			</li>	

			<li data-mcn-role="tab-header">
				<a data-toggle="tab" href="#tab-preview" data-control="tab-preview"><?php echo JText::_('COM_NEWSLETTER_PREVIEW'); ?></a>
			</li>	
		</ul>

		<div class="tab-content"  data-mcn-role="tab-content-container">
			<div id="tab-send" class="tab-pane active" data-mcn-role="tab-content">
				<?php echo $this->loadTemplate('send', ''); ?>
			</div>	
			<div id="tab-html" class="tab-pane" data-mcn-role="tab-content">
				<?php echo $this->loadTemplate('html', ''); ?>
			</div>	
			<div id="tab-plain" class="tab-pane" data-mcn-role="tab-content">
				<?php echo $this->loadTemplate('plain', ''); ?>
			</div>	
			<div id="tab-attachments" class="tab-pane" data-mcn-role="tab-content">
				<?php echo $this->loadTemplate('attachments', ''); ?>
			</div>	
			<div id="tab-preview" class="tab-pane" data-mcn-role="tab-content">
				<?php echo $this->loadTemplate('preview', ''); ?>
			</div>	
		</div>	

		<input type="hidden" name="newsletter_id" value="<?php echo $this->form->getValue('newsletter_id'); ?>" />
		<?php echo $this->form->getInput('newsletter_id'); ?>
		<?php echo $this->form->getInput('t_style_id'); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="jform[htmlTpl]" value="" />
		<input type="hidden" name="jform[plugins]" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
		
</div>
