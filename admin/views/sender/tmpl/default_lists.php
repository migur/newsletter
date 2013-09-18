<fieldset id="lists-fieldset">
<div class="legend"><?php echo JText::_('COM_NEWSLETTER_LISTS'); ?></div>

	<div class="inline-info">
		<span class="label label-info">#2</span>
		<span class="text-info"><?php echo JText::_('COM_NEWSLETTER_SENDMAIL_STEP2'); ?></span>
	</div>

    <form id="lists-form" name="listsForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=sender&tmpl=component'); ?>" method="post">
        <div id="sender-container">
            <table class="sslist adminlist  table table-striped" width="100%">
                <thead>
                    <tr>
                        <th class="left" width="1%">
                                <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
                        </th>
                        <th width="30%" class="left">
                                <?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_LIST_NAME', 'a.name', $this->lists->listDirn, $this->lists->listOrder); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($this->lists->items as $i => $item) : ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td>
                            <?php echo JHtml::_('multigrid.id', $i, $item->list_id, false, 'cid', 'listsForm'); ?>
                        </td>
                        <td>
                            <?php echo $this->escape($item->name); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="" />
                <?php echo JHtml::_('form.token'); ?>
        </div>

    </form>
</fieldset>
