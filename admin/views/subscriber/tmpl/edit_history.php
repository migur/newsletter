<?php
$listDirn  = 'a.date';
$listOrder = 'desc';
?>

<div id="history-container">
    <table class="sshistory adminlist" width="100%">
        <thead>
            <tr>
                <th width="30px" class="left">
                        <?php echo JHtml::_('grid.sort', '#', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th width="30%" class="left">
                        <?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_NEWSLETTER_NAME', 'a.name', $listDirn, $listOrder); ?>
                </th>
                <th width="50px" class="left">
                        <?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_ACTION', 'a.action', $listDirn, $listOrder); ?>
                </th>
                <th width="50px" class="left">
                        <?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_DATE', 'a.date', $listDirn, $listOrder); ?>
                </th>
                <th class="left">
                        <?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_INFO', 'a.text', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="5">
                    <?php echo $this->historyPagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php foreach ($this->historyItems as $i => $item) :
            $item->max_ordering = 0; //??
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td>
                    <?php echo $this->escape($i + 1); ?>
                </td>
                <td>
                    <?php echo $this->escape($item->name); ?>
                </td>
                <td style="white-space: nowrap">
                    <?php echo JText::_('COM_NEWSLETTER_HISTORY_' . $item->action); ?>
                </td>
                <td style="white-space: nowrap">
                    <?php echo $this->escape($item->date); ?>
                </td>
                <td>
                    <?php echo $this->escape($item->text); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>