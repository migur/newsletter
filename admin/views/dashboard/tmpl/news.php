<div id="news">
    <div class="legend"><?php echo JText::_('COM_NEWSLETTER_LATEST_NEWS'); ?></div>

    <ul>
		<?php 
		if (!empty($this->news['items'])) {
			foreach ($this->news['items'] as $item) { ?>
	        <li>
	            <a
	                href="<?php echo $this->escape($item['link']); ?>"
	                target="_blank"
				>
					<?php echo $this->escape($item['title']); ?>
				</a>
			</li>
		<?php 
			}
		} else {
			JText::_('There are no news');
		} ?>
    </ul>
</div>
