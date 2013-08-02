<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset id="tabs">
    <legend><?php echo JFactory::getDocument()->getTitle(); ?></legend>
    <form class="form-validate" method="POST" action="<?php echo JURI::base(); ?>index.php?option=com_newsletter&amp;view=list&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JUtility::getToken();?>=1" enctype="multipart/form-data" id="listForm" name="listForm">
    <?php
        echo JToolBar::getInstance('multitab-toolbar')->render();
        echo JHtml::_('tabs.start', 'tabs-list', array('startOffset'=> $this->activeTab));
        echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_OVERVIEW'), 'tab-overview');
        echo $this->loadTemplate('overview', '');
		
		if (
			( $this->isNew && NewsletterHelperAcl::actionIsAllowed('list.add')) ||
			(!$this->isNew && NewsletterHelperAcl::actionIsAllowed('list.edit'))
		) {
			echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_IMPORT'), 'tab-import');
			echo $this->loadTemplate('import', '');
			echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_EXCLUDE'), 'tab-exclude');
			echo $this->loadTemplate('exclude', '');
		}
		
        echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_SUBSCRIBERS'), 'tab-subscribers');
        echo $this->loadTemplate('subscribers', '');
        echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_UNSUBSCRIBED'), 'tab-unsubscribed');
        echo $this->loadTemplate('unsubscribed', '');
        echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_ADVANCED'), 'tab-advanced');
        echo $this->loadTemplate('advanced', '');
        echo JHtml::_('tabs.end');
    ?>

        <input type="hidden" name="list_id" value="<?php echo $this->listForm->getValue('list_id'); ?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="subtask" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</fieldset>

<script src="<?php echo JUri::base(); ?>/components/com_newsletter/views/list/tabs.js" type="text/javascript"></script>
