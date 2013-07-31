<?php
// no direct access
defined('_JEXEC') or die;
?>
<div id="tabs">
    <legend id="tabs-legend"><?php echo JFactory::getDocument()->getTitle(); ?></legend>
    <table width="100%">
        <tr>
            <td>
                <form name="newsletterForm" method="POST" id="tabs-sub-container" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">
                <?php
                    echo JHtml::_('tabs.start', 'tabs-newsletter');
                    echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_SEND'), 'tab-send');
                    echo $this->loadTemplate('send', '');
                    echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_HTML'), 'tab-html');
                    echo $this->loadTemplate('html', '');
                    echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_PLAIN'), 'tab-plain');
                    echo $this->loadTemplate('plain', '');
                    echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_ATTACHMENTS'), 'tab-attachments');
                    echo $this->loadTemplate('attachments', '');
                    echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_PREVIEW'), 'tab-preview');
                    echo $this->loadTemplate('preview', '');
                    echo JHtml::_('tabs.end');
                ?>
                    <input type="hidden" name="newsletter_id" value="<?php echo $this->form->getValue('newsletter_id'); ?>" />
                    <?php echo $this->form->getInput('newsletter_id'); ?>
                    <?php echo $this->form->getInput('t_style_id'); ?>
                    <input type="hidden" name="task" value="" />
                    <input type="hidden" name="jform[htmlTpl]" value="" />
                    <input type="hidden" name="jform[plugins]" value="" />
                    <?php echo JHtml::_('form.token'); ?>
                </form>


            </td>
            <td width="250px">
                <div id="trashcan-container" class="toolbar-list drop trashcan">
                    <ul class="hide"><li>
                    <span class="icon-32-trash">
                    </span>
                        Trash modules here!
                    </li></ul>
                </div>
                <?php echo $this->loadTemplate('accordions', ''); ?>
            </td>
        </tr>
    </table>
</div>
