<div class="import">
    
    <?php echo MigurToolBar::getInstance('import-toolbar')->render(); ?>

    <div class="toolbar-list">
        <ul>
        <?php foreach($this->importPlugins as $plg) { 
			$plg = (object) $plg; ?>
          <li class="button plugin-icon" rel="<?php echo !empty($plg->name)? $plg->name : ''; ?>" role="pluginButton">  
              <a href="#">
                  <span><img src="<?php echo !empty($plg->icon)? $plg->icon : ''; ?>" /></span>
                  <?php echo !empty($plg->title)? $plg->title : ''; ?>
              </a>    
          </li>
        <?php } ?>
        </ul>  
    </div>

	<div class="clr"></div>
	
	<div class="import-content">

		<div class="plugin-preloader"><div>
		<div class="plugin-pane">
		</div>

		<div class="clr"></div>
		<div id="import-file" class="file-form hide">
			
			<div class="block-notice">
				<?php echo JText::_('COM_NEWSLETTER_STANDARD_IMPORT_HELPTEXT'); ?>
				<?php echo JHtml::_('migurhelp.link', 'list', 'import'); ?>
			</div>
			
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
					<div class="drop pull-left" rel="username"></div>
					<div class="pull-left"><?php echo JText::_('COM_NEWSLETTER_USE_FIELD'); ?><br/><?php echo JText::_('COM_NEWSLETTER_AS') . ' ' . JText::_('COM_NEWSLETTER_SUBSCRIBER_NAME'); ?></div>
					<div class="clr"></div>
					<div class="drop pull-left" rel="email"></div>
					<div class="pull-left"><?php echo JText::_('COM_NEWSLETTER_USE_FIELD'); ?><br/><?php echo JText::_('COM_NEWSLETTER_AS') . ' ' . JText::_('JGLOBAL_EMAIL'); ?></div>
					<div class="clr"></div>
					<div class="drop pull-left" rel="html"></div>
					<div class="pull-left"><?php echo JText::_('COM_NEWSLETTER_USE_FIELD') . ' ' . JText::_('COM_NEWSLETTER_AS') . ' HTML.'?><br/>
						<?php echo JText::_('COM_NEWSLETTER_DEFAULT'); ?>
						<select id="import-file-html-default">
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>
					</div>	
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

					<div style="margin-top:10px;overflow:hidden">
						<input type="checkbox" id="import-overwrite" name="import_overwrite" />
						<div style="margin:5px; float: left;"><?php  echo JText::_('COM_NEWSLETTER_IMPORT_OVERWRITE'); ?></div>
					</div>
					<div style="overflow:hidden">
						<input type="checkbox" id="import-skip-header" name="import_skip_header" />
						<div style="margin:5px; float: left;">
							<?php echo JText::_('COM_NEWSLETTER_SKIP_HEADER'); ?>
							<?php echo JHtml::_('migurhelp.link', 'list', 'import'); ?>
						</div>
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

			<div class="fltrt submit-control">
				<div class="pull-left">
					<span id="import-message"></span>&nbsp;&nbsp;&nbsp;
					<div id="import-preloader" class="fltrt"></div>
				</div>

				<input
					type="button"
					name="newsletter_upload"
					onclick=""
					id="import-file-apply"
					value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_FILE_APPLY'); ?>"
				/>
			</div>	
		</div>
	</div>	
</div>