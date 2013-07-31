<div id="flash-form">
    <div class="upload-flash hide">
            <ul>
                    <li><a href="#" class="upload-browse" id="upload-browse"><?php echo JText::_('COM_MEDIA_BROWSE_FILES'); ?></a></li>
            </ul>
    <!--                    <div class="clr"> </div>-->
            <p class="overall-title"></p>
            <?php echo JHTML::_('image','media/bar.gif', JText::_('COM_MEDIA_OVERALL_PROGRESS'), array('class' => 'progress overall-progress'), true); ?>
    <!--                    <div class="clr"> </div>-->
            <p class="current-title"></p>
            <?php echo JHTML::_('image','media/bar.gif', JText::_('COM_MEDIA_CURRENT_PROGRESS'), array('class' => 'progress current-progress'), true); ?>
            <p class="current-text"></p>
    </div>

    <ul class="upload-queue">
        <li></li>
    </ul>
</div>
