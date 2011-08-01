    <table class="adminlist" width="100%">
        <thead>
            <tr>
                <th width="40%" class="left">
                    <?php echo JText::_('COM_NEWSLETTER_NAME'); ?>
                </th>
                <th width="40%" class="left">
                    <?php echo JText::_('COM_NEWSLETTER_EXTENSION'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->plugins as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td>
                        <?php echo $this->escape($item->title); ?>
                    </td>
                    <td>
                        <?php echo $this->escape($item->extension); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
