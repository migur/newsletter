<form 
	method="POST" 
	name="automailingtargetsForm" 
	id="automailingtargetsForm" 
	action="<?php echo JRoute::_('index.php?option=com_newsletter&tmpl=component&view=automailing&layout='.$this->getLayout().'&automailing_id='.$this->automailingId, false); ?>"
>
	<div class="fltrt">
		<select name="list_to_subscribe" class="inputbox">
			<option value=""><?php echo '- ' . JText::_('COM_NEWSLETTER_SELECT_LIST') . ' -'; ?></option>
			<?php
				foreach($this->unusedLists as $i => $item) { ?>
					<option value="<?php echo $this->escape($item->list_id); ?>">
						<?php echo $this->escape($item->name); ?>
					</option>
			<?php } ?>
		</select>
		<button type="submit"  onclick="return Joomla.submitform('automailing.assignList', $('automailingtargetsForm'));">
			<?php echo JText::_('COM_NEWSLETTER_ASSIGN'); ?>
		</button>
	</div>	
		
    <div id="sslist-container">
    <table class="sslist adminlist" width="100%">
        <thead>
            <tr>
                <th width="20px" class="left">
                        <?php echo JHtml::_('multigrid.sort', '#', 'a.list_id', $this->automailingTargets->listDirn, $this->automailingTargets->listOrder, null, null, 'automailingtargetsForm'); ?>
                </th>
                <th width="80%" class="left">
                        <?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_LIST_NAME', 'a.name', $this->automailingTargets->listDirn, $this->automailingTargets->listOrder, null, null, 'automailingtargetsForm'); ?>
                </th>
                <th width="15px" class="left">
                </th>
            </tr>
        </thead>
        <tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->automailingTargets->pagination->getListFooter(); ?>
				</td>
			</tr>
        </tfoot>
        <tbody>
        <?php foreach ($this->automailingTargets->items as $idx => $item) { ?>
            <tr class="row<?php echo $idx % 2; ?>">
                <td>
                        <?php echo $idx + 1; ?>
                </td>
                <td>
                        <?php echo $this->escape($item->name); ?>
                </td>
                <td class="center image-remove">
                    <a
                        href="#"
                        onclick="
                            $('list_to_unbind').set('value', '<?php echo $this->escape($item->list_id); ?>');
                            Joomla.submitform('automailing.unbindList', $('automailingtargetsForm'));
                            return false;"
                    >
                        <img
                            border="0" style="margin:0;"
                            alt="<?php echo JText::_('COM_NEWSLETTER_REMOVE'); ?>"
                            src="<?php echo JURI::root() . 'media/media/images/remove.png' ?>">
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
    <input type="hidden" name="filter_order" value="<?php echo $this->automailingTargets->listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->automailingTargets->listDirn; ?>" />
    <input type="hidden" name="automailing_id" value="<?php echo $this->automailingId; ?>" />
    <input type="hidden" name="list_to_unbind" value="" id="list_to_unbind"/>
    <input type="hidden" name="form" value="automailingtargets" id="list_to_unbind"/>
    <input type="hidden" name="task" value="" />

</form>

