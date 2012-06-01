<?php
// no direct access
defined('_JEXEC') or die;
?>

<table class="nl-automailings" style="width:100%">
    <tr>
        <td width="45%" style="vertical-align: top;">

            <form id="form-automailings" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=automailings&form=automailings');?>" method="post" name="automailingsForm" >
                <fieldset>
                <legend><?php echo JText::_('COM_NEWSLETTER_AUTOMAILINGS'); ?></legend>

                <fieldset class="filter-bar" >
					<div class="pull-left">
						<?php echo JToolBar::getInstance('automailings')->render(); ?>
					</div>	
					<div class="btn-toolbar pull-right">
						<a class="btn" data-target="#automailings-filter-panel" data-toggle="collapse">Filter: <span class="caret"></span></a>
					</div>
                </fieldset>
				<div id="automailings-filter-panel" class="collaps collapse">
					<div class="filter-select well">
						<div class="filter-search btn-group pull-left">
							<input type="text" name="filter_search" id="automailing_filter_search" class="migur-search" value="<?php echo $this->escape($this->automailings->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_NEWSLETTER_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_NEWSLETTER_FILTER_SEARCH_DESC'); ?>"/>
						</div>
						<div class="btn-group pull-left">
							<button type="submit" class="btn tip migur-search-submit" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
							<button rel="tooltip" onclick="document.id('automailing_filter_search').value='';this.form.submit();" type="button" class="btn tip" data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
						</div>
					</div>	
				</div>

                <table class="automailingslist adminlist  table table-striped" width="100%">
                        <thead>
                                <tr>
                                        <th width="1%">
                                                <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
                                        </th>
                                        <th class="left">
                                                <?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_AUTOMAILING', 'a.title', $this->automailings->listDirn, $this->automailings->listOrder, null, null, 'automailingsForm'); ?>
                                        </th>
                                </tr>
                        </thead>
                        <tfoot>
							<tr>
								<td colspan="2">
									<?php echo $this->pagination->getListFooter(); ?>
								</td>
							</tr>
                        </tfoot>
                        <tbody>
                        <?php
                                foreach ($this->automailings->items as $i => $item) {
                            ?>
                                <tr class="row<?php echo $i % 2; ?>">
									<td class="center">
										<?php
											$idx = $item->automailing_id;
											echo JHtml::_('multigrid.id', $i, $idx, false, 'cid', 'automailingsForm');
										?>
									</td>
									<td>
										<a href="<?php echo JRoute::_('index.php?option=com_newsletter&task=automailing.edit&tmpl=component&automailing_id='.(int) $item->automailing_id); ?>"
										   rel="{handler: 'iframe', size: {x: 820, y: 480} }"
										   class="modal" >
											<?php echo $this->escape($item->automailing_name); ?>
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
                                <input type="hidden" name="filter_order" value="<?php echo $this->automailings->listOrder; ?>" />
                                <input type="hidden" name="filter_order_Dir" value="<?php echo $this->automailings->listDirn; ?>" />
                                <?php echo JHtml::_('form.token'); ?>
                        </div>
                </fieldset>
            </form>
        </td>

        <td width="1%"></td>

        <td style="vertical-align: top;">
			
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#details">JText::_('COM_NEWSLETTER_PREVIEW')</a></li>
			</ul>
			<div class="tab-content">
				<div id="details" class="tab-pane active">
	                <iframe id="preview-container"></iframe>
				</div>	
			</div>
<!-- 2.5            <?php echo JHtml::_('tabs.start', 'prewiew'); ?>
            <?php echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_PREVIEW'), 'tab-preview'); ?>
                <iframe id="preview-container"></iframe>
            <?php echo JHtml::_('tabs.end'); ?> -->
        </td>
    </tr>
</table>
