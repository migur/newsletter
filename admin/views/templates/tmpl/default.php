<?php
// no direct access
defined('_JEXEC') or die;
?>

<?php echo JHtml::_('layout.wrapper'); ?>

<div id="templates-list" class="pull-left">
	<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=templates&form=templates');?>" method="post" name="adminForm" >
		
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_TEMPLATES'); ?></div>

		<div class="pull-left btn-group">
			<input type="text" name="filter_search" id="template_filter_search" class="migur-search" value="<?php echo $this->escape($this->templates->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_NEWSLETTER_FILTER_SEARCH_DESC'); ?>" />
		</div>	
		<div class="pull-left btn-group">
			<button type="submit" class="btn migur-search-submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="btn btn-danger" onclick="document.id('template_filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		
		<table class="templateslist adminlist  table table-striped" width="100%">
				<thead>
						<tr>
								<th width="1%">
										<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
								</th>
								<th class="left">
										<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_TEMPLATE', 'a.title', $this->templates->listDirn, $this->templates->listOrder, null, null, 'adminForm'); ?>
								</th>
						</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="2">
							<div class="pull-left">
								<?php echo $this->pagination->getListFooter(); ?>
							</div>	
							<div class="pull-right">
								<label for="limit" class="pull-left buttongroup-label"><?php echo JText::_('COM_NEWSLETTER_LIMIT'); ?></label>
								<?php echo $this->pagination->getLimitBox(); ?>
							</div>					
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
						foreach ($this->templates->items as $i => $item) {
					?>
						<tr class="row<?php echo $i % 2; ?>">
								<td class="center">
									<?php
										$idx = $item->t_style_id;
										echo JHtml::_('multigrid.id', $i, $idx, false, 'cid', 'adminForm');
									?>
								</td>
								<td>
								<a href="<?php echo JRoute::_('index.php?option=com_newsletter&task=template.edit&t_style_id='.(int) $item->t_style_id); ?>">
									<?php echo $this->escape($item->title); ?>
								</a>

									<a href="#" class="search icon-search"></a>
								</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>

				<div>
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="boxchecked" value="0" />
						<input type="hidden" name="filter_order" value="<?php echo $this->templates->listOrder; ?>" />
						<input type="hidden" name="filter_order_Dir" value="<?php echo $this->templates->listDirn; ?>" />
						<?php echo JHtml::_('form.token'); ?>
				</div>
	</form>
</div>	

<div id="templates-preview">

	<ul id="prewiew" class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#tabconf-general"><?php echo JText::_('COM_NEWSLETTER_PREVIEW'); ?></a></li>	
	</ul>

	<div class="tab-content">
		<div id="container-preloader"></div>
		<div id="tabconf-general" class="tab-pane active">
			<div><b><span id="tpl-title"></span></b></div>
			<br />
			<div><span id="tpl-name-label"><?php echo JText::_('COM_NEWSLETTER_AUTHOR') . ":"; ?>&nbsp;&nbsp;</span><span id="tpl-name"></span></div>
			<div><span id="tpl-email-label"><?php echo JText::_('COM_NEWSLETTER_AUTHOR_EMAIL') . ":"; ?>&nbsp;&nbsp;</span><span id="tpl-email"></span></div>
			<br />

			<div id="preview-container"></div>			
		</div>	
	</div>	
</div>	

<?php echo JHtml::_('layout.wrapperEnd'); ?>
