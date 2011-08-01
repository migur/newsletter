<?php
$listDirn  = 'a.name';
$listOrder = 'asc';
?>

<form action="<?php echo JRoute::_('index.php?option=com_newsletter&view=subscriber&tmpl=component&layout=') . $this->getLayout(); ?>" method="POST" name="listForm" id="listForm">
    <select name="list_to_subscribe" class="inputbox">
        <option value=""><?php echo '- ' . JText::_('COM_NEWSLETTER_SELECT_LIST') . ' -'; ?></option>
        <?php
            foreach($this->listItems as $i => $item) {
                if (empty($item->subscriber_id)) { ?>
                    <option value="<?php echo $this->escape($item->list_id); ?>">
                        <?php echo $this->escape($item->name); ?>
                    </option>
        <?php } } ?>
    </select>
    <button class="sslist-submit" type="submit"  onclick="return Joomla.submitform('subscriber.assign', $('listForm'));">
        <?php echo JText::_('COM_NEWSLETTER_ASSIGN_TO_LIST'); ?>
    </button>

    <div id="sslist-container">
    <table class="sslist adminlist" width="100%">
        <thead>
            <tr>
                <th width="20px" class="left">
                        <?php echo JHtml::_('multigrid.sort', '#', 'a.list_id', $listDirn, $listOrder, null, null, 'listForm'); ?>
                </th>
                <th width="80%" class="left">
                        <?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_LIST_NAME', 'a.name', $listDirn, $listOrder, null, null, 'listForm'); ?>
                </th>
                <th width="15px" class="left">
                </th>
            </tr>
        </thead>
        <tfoot>
        </tfoot>
        <tbody>
        <?php $idx = 0; foreach ($this->listItems as $i => $item) {
            if (!empty($item->subscriber_id)) {
            $item->max_ordering = 0; //??
            ?>
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
                            Joomla.submitform('subscriber.unbind', $('listForm'));
                            return false;"
                    >
                        <img
                            border="0" style="margin:0;"
                            alt="<?php echo JText::_('COM_NEWSLETTER_REMOVE'); ?>"
                            src="<?php echo JURI::root() . 'media/media/images/remove.png' ?>">
                    </a>
                </td>
            </tr>
            <?php $idx++; } } ?>
        </tbody>
    </table>
    </div>
    <input type="hidden" name="filter_order" value="<?php echo $this->subscribers->listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->subscribers->listDirn; ?>" />
    <input type="hidden" name="subscriber_id" value="<?php echo $this->subscriberId; ?>" />
    <input type="hidden" name="list_to_unbind" value="<?php echo $this->subscriberId; ?>" id="list_to_unbind"/>
    <input type="hidden" name="task" value="" />

</form>

