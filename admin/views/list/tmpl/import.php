<div class="import">
    
	<div id="import-toolbar">
		
		<button class="btn btn-large" data-role="import-native">
			<span><?php echo JText::_('COM_NEWSLETTER_IMPORT_FROM_FILE'); ?></span>
		</button>

		<?php foreach($this->importPlugins as $plg) { ?>

			<?php $plg = (object) $plg; ?>
			<button class="btn btn-large" data-role="import-plugin" rel="<?php echo !empty($plg->name)? $plg->name : ''; ?>" role="pluginButton">  
				<img src="<?php echo !empty($plg->icon)? $plg->icon : ''; ?>" />
				<span><?php echo !empty($plg->title)? $plg->title : ''; ?></span>
			  </a>    
		  </button>
		<?php } ?>
		
	</div>
	
	<div class="import-content">

		<div class="plugin-preloader"></div>
		<iframe class="plugin-pane" src="" scrolling="auto"></iframe>

		<div class="clr"></div>
		<div id="import-file" class="file-form hide">
			
			<div class="alert alert-info">
				<?php echo JText::_('COM_NEWSLETTER_STANDARD_IMPORT_HELPTEXT'); ?>
				<?php echo JHtml::_('migurhelp.link', 'list', 'import'); ?>
			</div>
			
			<iframe 
				id="import-uploader" 
				src="<?php echo JRoute::_("index.php?option=com_newsletter&tmpl=component&view=uploader&params[task]=list.upload&params[callback]=MigurImportUploadCallback&params[list_id]=". $this->listForm->getValue('list_id'), false); ?>">
			</iframe>

			<fieldset id="import-found-fields" class="drop">
				<div class="legend">
					<span><?php echo JText::_('COM_NEWSLETTER_IMPORT_FOUNDED_FIELDS'); ?></span>
					<input
						class="btn btn-info"
						type="button"
						name="newsletter_import_refresh"
						onclick=""
						id="import-file-refresh"
						value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_FILE_REFRESH'); ?>"
					/>
				</div>
			</fieldset>

			<fieldset id="import-fields">
				<div class="legend"><?php echo JText::_('COM_NEWSLETTER_IMPORT_DND_FIELDS'); ?></div>
				<div><?php echo JText::_('COM_NEWSLETTER_USE_FIELD') . JText::_('COM_NEWSLETTER_AS') . ' ' . JText::_('COM_NEWSLETTER_SUBSCRIBER_NAME'); ?></div>
				<div class="drop" rel="username"></div>
				<div><?php echo JText::_('COM_NEWSLETTER_USE_FIELD') . JText::_('COM_NEWSLETTER_AS') . ' ' . JText::_('JGLOBAL_EMAIL'); ?></div>
				<div class="drop" rel="email"></div>
				<div><?php echo JText::_('COM_NEWSLETTER_USE_FIELD') . ' ' . JText::_('COM_NEWSLETTER_AS') . ' HTML.'?></div>
				<div class="drop pull-left" rel="html"></div>
				<div class="pull-left">
					<span><?php echo JText::_('COM_NEWSLETTER_DEFAULT'); ?></span>
					<select id="import-file-html-default" class="input-small">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>
				</div>	
			</fieldset>

			<fieldset id="import-settings">
				<div class="legend"><?php echo JText::_('COM_NEWSLETTER_IMPORT_SETTINGS'); ?></div>
				
				<div class="control-group">
					<label class="control-label">
						<?php  echo JText::_('COM_NEWSLETTER_IMPORT_SELECT_DELIMITER'); ?>
					</label>
					<div class="controls" id="import-del-cont">
						<select name="import_delimiter" id ="import-delimiter" class="input-small">
							<option value=",">,</option>
							<option value=";">;</option>
							<option value="tab">tab</option>
							<option value="space">space</option>
						</select>
						
						<input 
							type="text"
							id="import-delimiter-custom" 
							name="import_delimiter_custom" 
							value="" 
							class="inputbox hide input-small"
						/>
						
						<input
							type="button"
							name="import_del_toggle_button"
							onclick=""
							id="import-del-toggle-button"
							value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_DEL_CUSTOM'); ?>"
							rel="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_DEL_SELECT'); ?>"
							class="btn btn-info"
						/>
					</div>
				</div>	

				<div class="control-group">
					<label class="control-label">
						<?php  echo JText::_('COM_NEWSLETTER_IMPORT_SELECT_ENCLOSURE'); ?>
					</label>
					<div class="controls">
						<select id="import-enclosure" name="import_enclosure" class="input-small">
							<option value="no">no</option>
							<option value="'">'</option>
							<option value='"'>"</option>
							<option value="`">`</option>
							<option value="#">#</option>
						</select>
						
						<input 
							type="text" 
							id="import-enclosure-custom" 
							name="import_enclosure_custom" 
							value="" 
							class="inputbox hide input-small"
						/>
						
						<input
							type="button"
							name="import_enc_toggle_button"
							onclick=""
							id="import-enc-toggle-button"
							value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_ENC_CUSTOM'); ?>"
							rel="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_ENC_SELECT'); ?>"
							class="btn btn-info"
						/>
					</div>
				</div>
				
				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" id="import-overwrite" name="import_overwrite" />
							<span><?php  echo JText::_('COM_NEWSLETTER_IMPORT_OVERWRITE'); ?></span>
						</label>
					</div>	
				</div>
				
				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" id="import-skip-header" name="import_skip_header" />
							<span>
								<?php echo JText::_('COM_NEWSLETTER_SKIP_HEADER'); ?>
								<?php echo JHtml::_('migurhelp.link', 'list', 'import'); ?>
							</span>
						</label>
					</div>	
				</div>
					
			</fieldset>


			<div class="pull-right">

				<input
					class="btn btn-success btn-large pull-right"
					type="button"
					name="newsletter_upload"
					onclick=""
					id="import-file-apply"
					value="<?php  echo JText::_('COM_NEWSLETTER_IMPORT_FILE_APPLY'); ?>"
				/>
								
				<div class="pull-right">
					<span id="import-message"></span>&nbsp;&nbsp;&nbsp;
					<div id="import-preloader" class="fltrt"></div>
				</div>

			</div>	
			
		</div>
	</div>	
</div>