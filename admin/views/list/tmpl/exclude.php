<div id="exclude-toolbar">
	
	<button id="exclude-toolbar-lists" class="btn btn-large">
		<?php echo JText::_('COM_NEWSLETTER_EXCLUDE_FROM_LISTS'); ?>
	</button>

	<button id="exclude-toolbar-file" class="btn btn-large">
		<?php echo JText::_('COM_NEWSLETTER_EXCLUDE_FROM_CSV'); ?>
	</button>
</div>

<div id="exclude-lists" class="hide">
    <div id="exclude-tab-scroller">
        <table class="sslist adminlist  table table-striped" id="table-exclude">
            <thead>
                <tr>
                    <th class="left" width="1%">
                        <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
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
		<input id="exclude-tab-button" class="btn btn-danger" type="button" value="<?php echo JText::_('COM_NEWSLETTER_EXCLUDE'); ?>">
		<div class="preloader-container"></div>
	</div>	

</div>

<div id="exclude-file" class="file-form hide">

	<iframe 
		id="exclude-uploader" 
		src="<?php echo JRoute::_("index.php?option=com_newsletter&tmpl=component&view=uploader&params[task]=list.upload&params[callback]=MigurExcludeUploadCallback&params[list_id]=". $this->listForm->getValue('list_id'), false); ?>"
		frameBorder="0"		
	>
	</iframe>
	
    <fieldset id="exclude-found-fields" class="drop">
        <div class="legend">
			<span><?php echo JText::_('COM_NEWSLETTER_EXCLUDE_FOUNDED_FIELDS'); ?></span>
			<input
				class="btn btn-info"
				type="button"
				name="newsletter_exclude_refresh"
				onclick=""
				id="exclude-file-refresh"
				value="<?php  echo JText::_('COM_NEWSLETTER_EXCLUDE_FILE_REFRESH'); ?>"
			/>
		</div>
    </fieldset>

    <fieldset id="exclude-fields">
        <div class="legend"><?php echo JText::_('COM_NEWSLETTER_EXCLUDE_DND_FIELDS'); ?></div>
		<div><?php echo JText::_('COM_NEWSLETTER_USE_FIELD') . JText::_('COM_NEWSLETTER_AS') . ' ' . JText::_('COM_NEWSLETTER_SUBSCRIBER_NAME'); ?></div>
		<div class="drop" rel="username"></div>
		<div><?php echo JText::_('COM_NEWSLETTER_USE_FIELD') . JText::_('COM_NEWSLETTER_AS') . ' ' . JText::_('JGLOBAL_EMAIL'); ?></div>
		<div class="drop" rel="email"></div>
    </fieldset>

	
	<fieldset id="exclude-settings">
		<div class="legend"><?php echo JText::_('COM_NEWSLETTER_EXCLUDE_SETTINGS'); ?></div>

		<div class="control-group">
			<label class="control-label">
				<?php  echo JText::_('COM_NEWSLETTER_EXCLUDE_SELECT_DELIMITER'); ?>
			</label>
			<div class="controls" id="exclude-del-cont">
				<select name="exclude_delimiter" id="exclude-delimiter" class="input-small">
					<option value=",">,</option>
					<option value=";">;</option>
					<option value="tab">tab</option>
					<option value="space">space</option>
				</select>

				<input 
					type="text"
					id="exclude-delimiter-custom" 
					name="exclude_delimiter_custom" 
					value="" 
					class="inputbox hide input-small"
				/>

				<input
					type="button"
					name="exclude_del_toggle_button"
					onclick=""
					id="exclude-del-toggle-button"
					value="<?php  echo JText::_('COM_NEWSLETTER_EXCLUDE_DEL_CUSTOM'); ?>"
					rel="<?php  echo JText::_('COM_NEWSLETTER_EXCLUDE_DEL_SELECT'); ?>"
					class="btn btn-info"
				/>
			</div>
		</div>	

		<div class="control-group">
			<label class="control-label">
				<?php  echo JText::_('COM_NEWSLETTER_EXCLUDE_SELECT_ENCLOSURE'); ?>
			</label>
			<div class="controls">
				<select id="exclude-enclosure" name="exclude_enclosure" class="input-small">
					<option value="no">no</option>
					<option value="'">'</option>
					<option value='"'>"</option>
					<option value="`">`</option>
					<option value="#">#</option>
				</select>

				<input 
					type="text" 
					id="exclude-enclosure-custom" 
					name="exclude_enclosure_custom" 
					value="" 
					class="inputbox hide input-small"
				/>

				<input
					type="button"
					name="exclude_enc_toggle_button"
					onclick=""
					id="exclude-enc-toggle-button"
					value="<?php  echo JText::_('COM_NEWSLETTER_EXCLUDE_ENC_CUSTOM'); ?>"
					rel="<?php  echo JText::_('COM_NEWSLETTER_EXCLUDE_ENC_SELECT'); ?>"
					class="btn btn-info"
				/>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" id="exclude-skip-header" name="exclude_skip_header" />
					<span>
						<?php echo JText::_('COM_NEWSLETTER_SKIP_HEADER'); ?>
						<?php echo JHtml::_('migurhelp.link', 'list', 'exclude'); ?>
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
			id="exclude-file-apply"
			value="<?php  echo JText::_('COM_NEWSLETTER_EXCLUDE_FILE_APPLY'); ?>"
		/>

		<div id="exclude-status-container" class="pull-right">
			<div id="exclude-message" class="pull-left"></div>
			<div class="pull-left">&nbsp;&nbsp;&nbsp;</div>
			<div id="exclude-preloader" class="pull-left"></div>
		</div>

	</div>	
	
</div>
