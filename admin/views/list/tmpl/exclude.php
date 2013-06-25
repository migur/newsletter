<div id="exclude-toolbar" class="toolbar-list">
    <ul>
        <li id="exclude-toolbar-lists" class="button">
            <a href="#">
                <span class="icon-32-remove">
                </span>
                Exclude from lists
            </a>
        </li>

        <li id="exclude-toolbar-file" class="button">
            <a href="#">
                <span class="icon-32-remove">
                </span>
                Exclude from CSV
            </a>
        </li>

    </ul>
    <div class="clr"></div>
</div>

<div class="clr"></div>

<div id="exclude-lists" class="hide">
    <div id="exclude-tab-scroller">
        <table class="sslist adminlist  table table-striped" id="table-exclude">
            <thead>
                <tr>
                    <th class="left" width="1%">
                        <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
                    </th>
                    <th class="left">
                        <?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_LIST_NAME', 'a.name', $this->lists->listDirn, $this->lists->listOrder, null, null, 'listsForm'); ?>
                    </th>
                    <th class="left" width="10%">
                        <?php echo JHtml::_('multigrid.sort', 'COM_NEWSLETTER_SUBSCRIBERS', 'subscribers', $this->lists->listDirn, $this->lists->listOrder, null, null, 'listsForm'); ?>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <?php // echo $this->lists->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <?php foreach ($this->lists->items as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td>
                        <?php echo JHtml::_('multigrid.id', $i, $item->list_id, false, 'cid', 'listForm'); ?>
                    </td>
                    <td>
                           <?php echo $this->escape($item->name); ?>
                    </td>
                    <td>
                        <?php
                               if (intval($item->subscribers) > 0) {
                                   echo $this->escape(intval($item->subscribers));
                               } else {
                                   echo '<span style="color:#cccccc">0</span>';
                               }
                        ?>
                   </td>
               </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

	<div id="excludelists-control-panel">
		<input id="exclude-tab-button" type="button" value="<?php echo JText::_('COM_NEWSLETTER_EXCLUDE'); ?>">
		<div class="preloader-container">&nbsp;</div>
	</div>	

</div>

<div id="exclude-file" class="file-form hide">
    <div id="exclude-file-upload">
        <fieldset id="exclude-uploadform">
                <legend><?php echo $this->config->get('upload_maxsize')=='0' ? JText::_('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></legend>
                <fieldset class="upload-noflash" class="actions">
                        <input type="file" id="exclude-upload-file" name="Filedata-exclude" size="70"/>
                        <input type="submit" id="exclude-upload-submit" value="<?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?>"/>
                        <ul class="upload-queue" id="upload-queue">
                            <li></li>
                        </ul>
                </fieldset>

                <input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_media'); ?>" />
                <input type="hidden" name="format" value="html" />
        </fieldset>
    </div>

    <fieldset id="exclude-founded-fields">
        <legend><?php echo JText::_('COM_NEWSLETTER_IMPORT_FOUNDED_FIELDS'); ?></legend>
		<div class="drop"></div>
    </fieldset>

    <fieldset id="exclude-fields">
        <legend><?php echo JText::_('COM_NEWSLETTER_IMPORT_DND_FIELDS'); ?></legend>
            <div class="drop fltlft" rel="username"></div>
			<div class="fltlft"><?php echo JText::_('COM_NEWSLETTER_USE_FIELD') ?><br/><?php echo JText::_('COM_NEWSLETTER_AS') . ' ' . JText::_('COM_NEWSLETTER_SUBSCRIBER_NAME'); ?></div>
            <div class="clr"></div>
            <div class="drop fltlft" rel="email"></div>
			<div class="fltlft"><?php echo JText::_('COM_NEWSLETTER_USE_FIELD'); ?><br/><?php echo JText::_('COM_NEWSLETTER_AS') . ' ' . JText::_('JGLOBAL_EMAIL'); ?></div>
    </fieldset>

    <fieldset id="exclude-settings">
        <legend><?php echo JText::_('COM_NEWSLETTER_IMPORT_SETTINGS'); ?></legend>
        <div style="margin: 5px 15px;">
            <?php  echo JText::_('COM_NEWSLETTER_IMPORT_SELECT_DELIMITER'); ?>
            <div id="exclude-del-cont">
                <select name="exclude_delimiter" id ="exclude-delimiter">
                    <option value=",">,</option>
                    <option value=";">;</option>
                    <option value="tab">tab</option>
                    <option value="space">space</option>
                </select>
                <input id="exclude-delimiter-custom" name="exclude_delimiter_custom" value="" class="hide">
            </div>

            <input
                type="button"
                name="exclude_del_toggle_button"
                onclick=""
                id="exclude-del-toggle-button"
                value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_DEL_CUSTOM'); ?>"
                rel="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_DEL_SELECT'); ?>"
            />

            <div class="clr"></div>

            <?php  echo JText::_('COM_NEWSLETTER_IMPORT_SELECT_ENCLOSURE'); ?>
            <div>
                <select id="exclude-enclosure" name="exclude_enclosure">
                    <option value="no">no</option>
                    <option value="'">'</option>
                    <option value='"'>"</option>
                    <option value="`">`</option>
                    <option value="#">#</option>
                </select>
                <input id="exclude-enclosure-custom" name="exclude_enclosure_custom" value="" class="hide">
            </div>

            <input
                type="button"
                name="exclude_enc_toggle_button"
                onclick=""
                id="exclude-enc-toggle-button"
                value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_ENC_CUSTOM'); ?>"
                rel="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_ENC_SELECT'); ?>"
            />

            <div class="clr"></div>
			
			<div style="margin-top:10px; overflow:hidden">
				<input type="checkbox" id="exclude-skip-header" name="exclude_skip_header" value="yes">
				<div style="margin:3px; float: left;"><?php  echo JText::_('COM_NEWSLETTER_SKIP_HEADER'); ?>
				</div>
			</div>
        </div>
    </fieldset>


    <input
        type="button"
        name="newsletter_exclude_refresh"
        onclick=""
        id="exclude-file-refresh"
        value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_FILE_REFRESH'); ?>"
    />


    <div class="fltrt submit-control">
        
        <div class="fltlft">
            <div id="exclude-preloader" class="fltrt"></div>
            <div id="exclude-message" class="fltlft"></div>
        </div>

        <input
            type="button"
            name="newsletter_upload"
            onclick=""
            id="exclude-file-apply"
            value="<?php  echo JText::_('COM_NEWSLETTER_EXCLUDE_FILE_APPLY'); ?>"
        />
    </div>	
    
    


</div>
