<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset class="template-new">
<legend><?php echo JText::_('COM_NEWSLETTER_SELECT_STANDARD_TEMPLATE'); ?></legend>	
<table class="nl-templates  table table-striped" style="width:100%">
    <tr>
        <td width="45%" style="vertical-align: top;">

		    <form name="templateForm" method="POST" id="form-template" class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">
                <fieldset class="standard-fieldset">
                <legend><?php echo JText::_('COM_NEWSLETTER_TEMPLATES'); ?></legend>

                <table class="templateslist adminlist  table table-striped" width="100%">
                        <thead>
                                <tr>
									<th width="40%" class="left">
											<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_TEMPLATE', 'a.title', $this->templates->listDirn, $this->templates->listOrder, null, null, 'templateForm'); ?>
									</th>
                                </tr>
                        </thead>
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
										<a href="#" class="search icon-16-search"></a>
										<input type="hidden" name="cid[]" value="<?php echo $item->template; ?>" />

									</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div>
                                <input type="hidden" name="task" value="" />
                                <input type="hidden" name="template" value="0" />
                                <input type="hidden" name="filter_order" value="<?php echo $this->templates->listOrder; ?>" />
                                <input type="hidden" name="filter_order_Dir" value="<?php echo $this->templates->listDirn; ?>" />
                                <?php echo JHtml::_('form.token'); ?>
                        </div>
                </fieldset>
            </form>
        </td>

        <td style="vertical-align: top;">
			<fieldset class="preview-fieldset">
			<legend><?php echo JText::_('COM_NEWSLETTER_PREVIEW'); ?></legend>	

                <div><b><span id="tpl-title"></span></b></div>
                <br />

                <div><span id="tpl-name-label"><?php echo JText::_('COM_NEWSLETTER_AUTHOR') . ":"; ?>&nbsp;&nbsp;</span><span id="tpl-name"></span></div>
                <div><span id="tpl-email-label"><?php echo JText::_('COM_NEWSLETTER_AUTHOR_EMAIL') . ":"; ?>&nbsp;&nbsp;</span><span id="tpl-email"></span></div>
                <br />

                <div id="preview-container" class="preview-container-new"></div>
			</fieldset>	
        </td>
    </tr>
</table>
</fieldset>
