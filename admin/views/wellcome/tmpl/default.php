<?php
// no direct access
defined('_JEXEC') or die;
?>

<form action="<?php echo JRoute::_('?option=com_newsletter&task=configuration.init')?>" method="POST">

	<h1><?php echo JText::_('Congratulations! You have just successfully installed the Migur Newsletter component!'); ?></h1>

	<?php if (!empty($this->backups)) : ?>
	<fieldset id="backup-panel">

		<div id="table-backup-desc">
			<?php echo JText::_('We found the previous versions of component\'s tables during installation.'); ?>
		</div>

		<span><?php echo JText::_('This tables were backed up'). ':'; ?></span>
		<ul>
		<?php foreach($this->backups as $table) : ?>
			<li><?php echo $table['original'] . ' - <span class="table-backup">' . $table['backup'] . '</span>'; ?></li>
		<?php endforeach; ?>
		</ul>
		
		<div id="backup-question">
			<b><?php echo JText::_('Do you want to delete these backups?'); ?></b>
			<br/>
			<input name="delete_backups" type="checkbox" value="1" />
			<span id="delete-backups-label"><?php echo JText::_('yes, I want delete these backups!'); ?></span>
		</div>
		
	</fieldset>
	<?php endif; ?>

	<center>
		<input type="submit" value="<?php echo JText::_('COM_NEWSLETTER_GOTO_DASHBOARD'); ?>" />
	</center>
</form>
