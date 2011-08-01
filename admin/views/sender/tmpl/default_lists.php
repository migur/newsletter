<fieldset id="lists-fieldset">
<legend><?php echo JText::_('COM_NEWSLETTER_LISTS'); ?></legend>

    <form id="lists-form" name="listsForm" action="<?php echo JRoute::_('index.php?option=com_newsletter&view=sender&tmpl=component'); ?>" method="post">
        <div id="sender-container">
            <table class="sslist adminlist" width="100%">
                <thead>
                    <tr>
                        <th class="left" width="1%">
                                <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
                        </th>
                        <th width="30%" class="left">
                                <?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_NEWSLETTER_NAME', 'a.name', $this->lists->listDirn, $this->lists->listOrder); ?>
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <?php echo $this->lists->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                </tfoot>
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