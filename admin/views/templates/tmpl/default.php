<?php
// no direct access
defined('_JEXEC') or die;
?>

<table class="nl-templates" style="width:100%">
    <tr>
        <td width="45%" style="vertical-align: top;">

            <form id="form-templates" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=templates&form=templates');?>" method="post" name="templatesForm" >
                <fieldset>
                <legend><?php echo JText::_('COM_NEWSLETTER_TEMPLATES'); ?></legend>

                <fieldset class="filter-bar" >
                    <?php echo JToolBar::getInstance('templates')->render(); ?>
                    <div id="templates-filter-panel-control" class="filter-panel-control"></div>
                    <div class="clr"></div>
                    <div id="templates-filter-panel" class="filter-panel">
						<div class="fltlft">
							<input type="text" name="filter_search" id="template_filter_search" class="migur-search" value="<?php echo $this->escape($this->templates->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_NEWSLETTER_FILTER_SEARCH_DESC'); ?>" />
							<button type="submit" class="migur-search-submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
							<button type="button" onclick="document.id('template_filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
						</div>
                    </div>
                </fieldset>
                <table class="templateslist adminlist  table table-striped" width="100%">
                        <thead>
                                <tr>
                                        <th width="1%">
                                                <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
                                        </th>
                                        <th class="left">
                                                <?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_TEMPLATE', 'a.title', $this->templates->listDirn, $this->templates->listOrder, null, null, 'templatesForm'); ?>
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
                                foreach ($this->templates->items as $i => $item) {
                            ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                        <td class="center">
                                            <?php
                                                $idx = $item->t_style_id;
                                                echo JHtml::_('multigrid.id', $i, $idx, false, 'cid', 'templatesForm');
                                            ?>
                                        </td>
                                        <td>
											<a href="<?php echo JRoute::_('index.php?option=com_newsletter&task=template.edit&tmpl=component&t_style_id='.(int) $item->t_style_id); ?>"
											   rel="{handler: 'iframe', size: {x: 820, y: 430} }"
											   class="modal" >
												<?php echo $this->escape($item->title); ?>
											</a>

                                            <a href="#" class="search icon-16-search"></a>
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
                </fieldset>
            </form>
        </td>

        <td width="1%"></td>

        <td style="vertical-align: top;">
			<ul class="nav nav-tab">
            <?php echo JHtml::_('tabs.start', 'prewiew'); ?>
            <?php echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_PREVIEW'), 'tab-preview'); ?>
                <div><b><span id="tpl-title"></span></b></div>
                <br />

                <div><span id="tpl-name-label"><?php echo JText::_('COM_NEWSLETTER_AUTHOR') . ":"; ?>&nbsp;&nbsp;</span><span id="tpl-name"></span></div>
                <div><span id="tpl-email-label"><?php echo JText::_('COM_NEWSLETTER_AUTHOR_EMAIL') . ":"; ?>&nbsp;&nbsp;</span><span id="tpl-email"></span></div>
                <br />

                <div id="preview-container"></div>


            <?php echo JHtml::_('tabs.end'); ?>
        </td>
    </tr>
</table>
