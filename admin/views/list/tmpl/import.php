<?php    echo MigurToolBar::getInstance('import-toolbar')->render(); ?>

<div class="clr"></div>
<div id="import-file" class="file-form hide">
    <div id="import-file-upload">
        <fieldset id="import-uploadform">
                <legend><?php echo $this->config->get('upload_maxsize')=='0' ? JText::_('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></legend>
                <fieldset class="upload-noflash" class="actions">
                        <input type="file" id="import-upload-file" name="Filedata-import" size="70"/>
                        <input type="submit" id="import-upload-submit" value="<?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?>"/>
                        <ul class="upload-queue">
                            <li></li>
                        </ul>
                </fieldset>

                <?php   echo $this->loadTemplate('uploadform', ''); ?>

                <input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_media'); ?>" />
                <input type="hidden" name="format" value="html" />
        </fieldset>
    </div>


    <fieldset id="import-founded-fields" class="drop">
        <legend><?php echo JText::_('COM_NEWSLETTER_IMPORT_FOUNDED_FIELDS'); ?></legend>
    </fieldset>

    <fieldset id="import-fields">
        <legend><?php echo JText::_('COM_NEWSLETTER_IMPORT_DND_FIELDS'); ?></legend>
            <div style="float:left"><?php echo JText::_('COM_NEWSLETTER_USE_FIELD'); ?></div><div class="drop" style="float:left" rel="username"></div><div style="float:left"><?php echo JText::_('COM_NEWSLETTER_AS') . ' ' . JText::_('COM_NEWSLETTER_SUBSCRIBER_NAME'); ?></div>
            <div class="clr"></div>
            <div style="float:left"><?php echo JText::_('COM_NEWSLETTER_USE_FIELD'); ?></div><div class="drop" style="float:left" rel="email"></div><div style="float:left"><?php echo JText::_('COM_NEWSLETTER_AS') . ' ' . JText::_('JGLOBAL_EMAIL'); ?></div>
            <div class="clr"></div>
            <div style="float:left"><?php echo JText::_('COM_NEWSLETTER_USE_FIELD'); ?></div><div class="drop" style="float:left" rel="html"></div><div style="float:left"><?php echo JText::_('COM_NEWSLETTER_AS') . ' HTML. ' . JText::_('COM_NEWSLETTER_DEFAULT'); ?></div>
            <select id="import-file-html-default" style="float:left">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
    </fieldset>

    <fieldset id="import-settings">
        <legend><?php echo JText::_('COM_NEWSLETTER_IMPORT_SETTINGS'); ?></legend>
        <div style="margin: 5px 15px;">
            <?php  echo JText::_('COM_NEWSLETTER_IMPORT_SELECT_DELIMITER'); ?>
            <div id="import-del-cont">
                <select name="import_delimiter" id ="import-delimiter">
                    <option value=",">,<option>
                    <option value=";">;<option>
                    <option value="tab">tab<option>
                    <option value="space">space<option>
                </select>
                <input id="import-delimiter-custom" name="import_delimiter_custom" value="" class="hide">
            </div>

            <input
                type="button"
                name="import_del_toggle_button"
                onclick=""
                id="import-del-toggle-button"
                value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_DEL_CUSTOM'); ?>"
                rel="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_DEL_SELECT'); ?>"
            />

            <div class="clr"></div>

            <?php  echo JText::_('COM_NEWSLETTER_IMPORT_SELECT_ENCLOSURE'); ?>
            <div>
                <select id="import-enclosure" name="import_enclosure">
                    <option value="no">no<option>
                    <option value="'">'<option>
                    <option value='"'>"<option>
                    <option value="`">`<option>
                    <option value="#">#<option>
                </select>
                <input id="import-enclosure-custom" name="import_enclosure_custom" value="" class="hide">
            </div>

            <input
                type="button"
                name="import_enc_toggle_button"
                onclick=""
                id="import-enc-toggle-button"
                value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_ENC_CUSTOM'); ?>"
                rel="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_ENC_SELECT'); ?>"
            />

            <div class="clr"></div>

            <div style="margin-top:10px;">
                <input type="checkbox" id="import-overwrite" name="import_overwrite" value="yes">
                <div style="margin:3px; float: left;"><?php  echo JText::_('COM_NEWSLETTER_IMPORT_OVERWRITE'); ?></div>
            </div>
        </div>
    </fieldset>


    <input
        type="button"
        name="newsletter_import_refresh"
        onclick=""
        id="import-file-refresh"
        value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_FILE_REFRESH'); ?>"
    />


    <input
        type="button"
        name="newsletter_upload"
        onclick=""
        id="import-file-apply"
        value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_FILE_APPLY'); ?>"
    />

</div>
