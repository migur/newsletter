<?php
// no direct access
defined('_JEXEC') or die;
?>

<form name="templateForm" method="POST" id="adminForm" class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=template', false); ?>">
	<div id="container-list">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_SELECT_STANDARD_TEMPLATE'); ?></div>	

		<table class="templateslist adminlist  table table-striped" width="100%">
			<tfoot>
					<tr>
						<td>
						</td>
					</tr>
			</tfoot>
			<tbody>
			<?php
					foreach ($this->templates->items as $i => $item) {
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td>
							<a href="#" onclick="document.templateForm.template.value='<?php echo $item->template; ?>'; Joomla.submitbutton('template.create');">
							   <?php echo $this->escape($item->title); ?>
							</a>	
							<a href="#" class="icon icon-search pull-right"></a>
							<input type="hidden" name="cid[]" value="<?php echo $item->template; ?>" />

						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
	</div>
	<div id="container-preview">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_PREVIEW'); ?></div>	
		<div id="container-preloader"></div>
		<div><b><span id="tpl-title"></span></b></div>
		<br />

		<div><span id="tpl-name-label"><?php echo JText::_('COM_NEWSLETTER_AUTHOR') . ":"; ?>&nbsp;&nbsp;</span><span id="tpl-name"></span></div>
		<div><span id="tpl-email-label"><?php echo JText::_('COM_NEWSLETTER_AUTHOR_EMAIL') . ":"; ?>&nbsp;&nbsp;</span><span id="tpl-email"></span></div>
		<br />

		<div id="preview-container" class="preview-container-new"></div>
	</div>	
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="template" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->templates->listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->templates->listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	
</form>
