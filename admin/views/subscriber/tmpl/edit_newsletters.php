<?php
$listDirn  = 'a.date';
$listOrder = 'desc';
?>

<div id="nllist-container">
<table class="ssnewsletters adminlist" width="100%">
    <thead>
        <tr>
            <th width="10%" class="left">
                    <?php echo JHtml::_('grid.sort', '#', 'a.title', $listDirn, $listOrder); ?>
            </th>
            <th width="45%" class="left">
                    <?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_NEWSLETTER', 'a.name', $listDirn, $listOrder); ?>
            </th>
            <th class="left">
                    <?php echo JHtml::_('grid.sort', 'COM_NEWSLETTER_DATE_SENT', 'a.sent_date', $listDirn, $listOrder); ?>
            </th>
        </tr>
    </thead>
    <tfoot>
    </tfoot>
    <tbody>
    <?php foreach ($this->newsletterItems as $i => $item) :
        $item->max_ordering = 0; //??
        ?>
        <tr class="row<?php echo $i % 2; ?>">
            <td>
                <?php echo $this->escape($i + 1); ?>
            </td>
            <td>
                <?php echo $this->escape($item->name); ?>
            </td>
            <td>
                <?php echo $this->escape($item->sent_date); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
