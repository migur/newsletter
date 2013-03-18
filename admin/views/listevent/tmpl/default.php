<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="automailing-new">

	<form name="adminForm" method="POST" class="form-horizontal form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter&tmpl=component'); ?>">

        <div class="control-group">
            <label class="control-label"><?php echo $this->form->getLabel('event'); ?></label>
            <div class="controls">
                <?php echo $this->form->getInput('event'); ?>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label"><?php echo $this->form->getLabel('jgroup_id'); ?></label>
            <div class="controls">
                <?php echo $this->form->getInput('jgroup_id'); ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo $this->form->getLabel('action'); ?></label>
            <div class="controls">
                <?php echo $this->form->getInput('action'); ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo $this->form->getInput('list_id'); ?></label>
            <div class="controls">
                <?php echo $this->form->getInput('le_id'); ?>
            </div>
        </div>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="le_id" value="<?php echo $this->form->getValue('le_id'); ?>" />
		<input type="hidden" name="list_id" value="<?php echo JRequest::getInt('list_id', ''); ?>" />
		<?php echo JHtml::_('form.token'); ?>
		
    	<div class="form-actions">
    		<?php echo JToolBar::getInstance('listevent')->render(); ?>
        </div>    
	</form>
</div>