<?php
// no direct access
defined('_JEXEC') or die;
?>

<fieldset class="automailing-new">
<legend><?php echo JText::_('COM_NEWSLETTER_SELECT_STANDARD_AUTOMAILING'); ?></legend>	
<table class="nl-automailings" style="width:100%">
    <tr>
        <td width="45%" style="vertical-align: top;">

		    <form name="automailingForm" method="POST" id="form-automailing" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">
				
                <?php echo $this->form->getLabel('automailing_name'); ?>
                <?php echo $this->form->getInput('automailing_name'); ?>

                <?php echo $this->form->getLabel('automailing_agregatedtype'); ?>
                <?php echo $this->form->getInput('automailing_agregatedtype'); ?>
				
				<table><tr><td>
					<fieldset>
						<legend><?php echo JText::_('COM_NEWSLETTER_SERIES'); ?></legend>	
						
						<?php 
						foreach($this->series as $idx => $serie) { ?>
							<div class="item">
								<div class="close"></div>
								<span class="date"></span>
								<span class="name"></span>
							</div> 
						<?php if ($idx < count($this->series)-1) { ?>
							<div class="arrow"></div>
						<?php }} ?>	
						
					</fieldset>
				</td></tr><tr><td>			
					<fieldset>
						<legend><?php echo JText::_('COM_NEWSLETTER_LISTS'); ?></legend>	
					</fieldset>
				</td></tr></table>	
				
                <fieldset class="standard-fieldset">
                <legend><?php echo JText::_('COM_NEWSLETTER_AUTOMAILINGS'); ?></legend>

                <table class="automailingslist adminlist" width="100%">
                        <thead>
                                <tr>
									<th width="40%" class="left">
											<?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_AUTOMAILING', 'a.title', $this->automailings->listDirn, $this->automailings->listOrder, null, null, 'automailingForm'); ?>
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
                                foreach ($this->automailings->items as $i => $item) {
                            ?>
                                <tr class="row<?php echo $i % 2; ?>">
									<td>
										<a href="#" onclick="document.automailingForm.automailing.value='<?php echo $item->automailing; ?>'; Joomla.submitbutton('automailing.create');">
										   <?php echo $this->escape($item->title); ?>
										</a>	
										<a href="#" class="search icon-16-search"></a>
										<input type="hidden" name="cid[]" value="<?php echo $item->automailing; ?>" />

									</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div>
							<input type="hidden" name="task" value="" />
							<input type="hidden" name="automailing_id" value="0" />
							<input type="hidden" name="filter_order" value="<?php echo $this->automailings->listOrder; ?>" />
							<input type="hidden" name="filter_order_Dir" value="<?php echo $this->automailings->listDirn; ?>" />
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
