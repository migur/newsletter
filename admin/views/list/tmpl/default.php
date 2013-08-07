<?php
// no direct access
defined('_JEXEC') or die;

$isAuthorised =
			( $this->isNew && NewsletterHelperAcl::actionIsAllowed('list.add')) ||
			(!$this->isNew && NewsletterHelperAcl::actionIsAllowed('list.edit'));
?>


<div id="modal-listevent" class="modal hide fade">
	<div class="modal-header">
		<button data-dismiss="modal" class="close" type="button">×</button>
		<h3><?php echo JText::_('COM_NEWSLETTER_LIST_EVENT'); ?></h3>
	</div>
	<div class="preloader-container"></div>
	<div class="modal-body"></div>
</div>



<div id="tabs">
    <form class="form-horizontal form-validate" method="POST" action="<?php echo JURI::base(); ?>index.php?<?php echo $this->session->getName().'='.$this->session->getId(); ?>&<?php //echo JUtility::getToken();?>=1" enctype="multipart/form-data" id="listForm" name="listForm">

        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab-overview" data-toggle="tab"><?php echo JText::_('COM_NEWSLETTER_OVERVIEW'); ?></a>
            </li>
            <?php if ($isAuthorised): ?>
            <li>
                <a href="#tab-import" data-toggle="tab"><?php echo JText::_('COM_NEWSLETTER_IMPORT'); ?></a>
            </li>
            <li>
                <a href="#tab-exclude" data-toggle="tab"><?php echo JText::_('COM_NEWSLETTER_EXCLUDE'); ?></a>
            </li>
            <?php endif; ?>
            <li>
                <a href="#tab-subscribers" data-toggle="tab"><?php echo JText::_('COM_NEWSLETTER_SUBSCRIBERS'); ?></a>
            </li>
            <li>
                <a href="#tab-unsubscribed" data-toggle="tab"><?php echo JText::_('COM_NEWSLETTER_UNSUBSCRIBED'); ?></a>
            </li>
            <li>
                <a href="#tab-advanced" data-toggle="tab"><?php echo JText::_('COM_NEWSLETTER_ADVANCED'); ?></a>
            </li>
        </ul>
        <div class="tab-content">

            <div class="tab-pane active" id="tab-overview">
                <?php echo $this->loadTemplate('overview', ''); ?>
            </div>

            <?php if ($isAuthorised): ?>

            <div class="tab-pane" id="tab-import">
                <?php echo $this->loadTemplate('import', ''); ?>
            </div>

            <div class="tab-pane" id="tab-exclude">
                <?php echo $this->loadTemplate('exclude', ''); ?>
            </div>

            <?php endif; ?>

            <div class="tab-pane" id="tab-subscribers">
                <?php echo $this->loadTemplate('subscribers', ''); ?>
            </div>

            <div class="tab-pane" id="tab-unsubscribed">
                <?php echo $this->loadTemplate('unsubscribed', ''); ?>
            </div>

            <div class="tab-pane" id="tab-advanced">
                <?php echo $this->loadTemplate('advanced', ''); ?>
            </div>

        </div>


        <input type="hidden" name="option" value="com_newsletter" />
        <input type="hidden" name="view" value="list" />
        <input type="hidden" name="list_id" value="<?php echo $this->listForm->getValue('list_id'); ?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="subtask" value="" />
        <?php echo JHtml::_('form.token'); ?>

    </form>
</div>

<script src="<?php echo JUri::base(); ?>/components/com_newsletter/views/list/tabs.js" type="text/javascript"></script>
