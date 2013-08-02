<?php
// no direct access
defined('_JEXEC') or die;

echo JHTML::_('layout.wrapper');
?>
	<form class="form-validate" name="statisticForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=statistic'); ?>" method="post">

    <div id="main-container">
        <?php echo $this->loadTemplate('letters'); ?>
        <?php echo $this->loadTemplate('clicks'); ?>
    </div>

	<input type="hidden" name="newsletters" value="<?php echo $this->ids; ?>">
	</form>
<?php echo JHTML::_('layout.wrapperEnd'); ?>