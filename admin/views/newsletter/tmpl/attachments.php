    <div id="attachments" class="form-text">
        <dl id="att-controls">
            <div class="fltrt">
				<a
					rel="{handler: 'iframe', size: {x: 700, y: 445}}"
					href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid=fileattach&amp;folder="
					class="modal button"
					id="newsletter_upload"
					name="newsletter_upload">
					
					<?php  echo JText::_('COM_NEWSLETTER_UPLOAD'); ?>
				</a>
				<input type="hidden" id="fileattach" name="fileattach" />
            </div>
        </dl>

        <div id="attlist-container">
        <table class="attlist adminlist" width="100%">
            <thead>
                <tr>
                    <th width="50%" class="left">
                            <?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_FILENAME', 'a.filename', $this->attItemslistDirn, $this->attItemslistOrder, null, null, 'attForm'); ?>
                    </th>
                    <th class="left">
                            <?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_FILESIZE', 'a.filesize', $this->attItemslistDirn, $this->attItemslistOrder, null, null, 'attForm'); ?>
                    </th>
                    <th width="30px" class="left">
                            <?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_FILETYPE', 'a.type', $this->attItemslistDirn, $this->attItemslistOrder, null, null, 'attForm'); ?>
                    </th>
                    <th width="15px">
                    </th>
                </tr>
            </thead>
            <tfoot>
            </tfoot>
            <tbody>
				<?php foreach ($this->downloads as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td>
                            <?php echo $this->escape($item->filename); ?>
                    </td>
                    <td>
                            <?php echo JHtml::_('file.size', $item->size, 'kb/mb'); ?>
                    </td>
                    <td>
                            <?php echo $this->escape($item->type); ?>
                    </td>
                    <td class="center">
                        <a href="#" class="remove-link" rel="<?php echo $item->downloads_id; ?>" >
                            <img
                                border="0" style="margin:0;"
                                alt="<?php echo JText::_('COM_NEWSLETTER_REMOVE'); ?>"
                                src="<?php echo JURI::root() . 'media/media/images/remove.png' ?>">
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
