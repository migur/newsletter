<fieldset id="news">
    <legend><?php echo JText::_('COM_NEWSLETTER_LATEST_NEWS'); ?></legend>

    <ul>
		<?php foreach ($this->news['items'] as $item) : ?>
	        <li>
	            <a
	                href="<?php echo $this->escape($item['link']); ?>"
	                target="_blank"
				>
					<?php echo $this->escape($item['title']); ?>
				</a>
			</li>
		<?php endforeach; ?>

    </ul>
</fieldset>
