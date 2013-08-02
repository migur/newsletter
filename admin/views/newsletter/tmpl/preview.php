<div id="tab-preview-container">

		<fieldset class="autocompleter-migur email-container">
				<ul class="autocompleter-items"></ul>
				<div class="clr"></div>
				<?php echo $this->form->getInput('newsletter_preview_email'); ?>
				<div>
					<input
						type="button"
						onclick=""
						id="button-newsletter-send-preview"
						value="<?php  echo JText::_('COM_NEWSLETTER_SEND_PREVIEW'); ?>"
					/>
					<div id="send-preview-preloader"></div>
				</div>	

		</fieldset>


        <?php echo JHtml::_('tabs.start', 'tab-preview-tabs'); ?>
            
        <?php echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_HTML'), 'tab-preview-html'); ?>
            <iframe src="" id="tab-preview-html-container"></iframe>

        <?php echo JHtml::_('tabs.panel', JText::_('COM_NEWSLETTER_PLAIN'), 'tab-preview-plain'); ?>
            <textarea id="tab-preview-plain-container" readonly></textarea>
            
        <?php echo JHtml::_('tabs.end'); ?>

</div>


<!--<form action="http://del.icio.us/search/" method="get" id="form-demo">
</form>-->