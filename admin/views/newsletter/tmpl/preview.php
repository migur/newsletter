<div id="tab-preview-container">

    <div class="autocompleter-migur email-container">
        <ul class="autocompleter-items"></ul>
        <div>
            <?php echo $this->form->getInput('newsletter_preview_email'); ?>
            &nbsp;&nbsp;
            <button id="button-newsletter-send-preview" class="btn btn-success" data-control="preview-send">
                <?php  echo JText::_('COM_NEWSLETTER_SEND_PREVIEW'); ?>
            </button>
            <div id="send-preview-preloader"></div>
        </div>
    </div>

    <div class="tabbable tabs-right">    
        <ul class="nav nav-tabs tab-preview-tabs span2 pull-right">
            <li class="active">
                <a data-toggle="tab" href="#tab-preview-html">
                    <?php echo JText::_('COM_NEWSLETTER_HTML'); ?>
                </a>
            </li>    
            <li>
                <a data-toggle="tab" href="#tab-preview-plain">
                    <?php echo JText::_('COM_NEWSLETTER_PLAIN'); ?>
                </a>
            </li>
        </ul>
        <div class="tab-content span9 pull-left">
            <div id="tab-preview-html" class="tab-pane active">    
                <iframe src="" id="tab-preview-html-container" frameBorder="0"></iframe>
            </div>
            <div id="tab-preview-plain" class="tab-pane">    
                <div id="tab-preview-plain-container"></div>
            </div>
        </div>
    </div>
</div>