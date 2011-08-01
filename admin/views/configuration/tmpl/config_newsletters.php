<?php $items = $this->form->getFieldset('newsletters'); ?>

<table class="adminlist" width="100%">
        <thead>
            <tr>
                <th width="40%" class="left">
                    <?php echo JText::_('COM_NEWSLETTER_CONFIG_NAME'); ?>
                </th>
                <th width="40%" class="left">
                    <?php echo JText::_('COM_NEWSLETTER_CONFIG_VALUE'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td>
                        <?php echo $item->label; ?>
                    </td>
                    <td>
                        <?php echo $item->input; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
