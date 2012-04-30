<fieldset>
    <legend><?php echo JText::_($this->plugin->title); ?></legend>
	
	<?php if (!empty($this->plugin->data['helpText'])) { ?>
		<span class="helptext">
			<?php echo JText::_($this->plugin->data['helpText']); ?>
		</span>
	<?php } ?>

	
    <form id="plugin-form" action="<?php echo JUri::current(); ?>">
        <table class="adminlist">
            <thead>
                <tr>
                <?php 
                    $visibleCols = array();
                    foreach($this->plugin->data['head'] as $key => $item) { 
                        if (!empty($item)) { ?>
                        <th>
                            <?php array_push($visibleCols, $key); echo JText::_($item); ?>
                        </th>
                <?php } } ?>
                </tr>    
            </thead>    
            <tfoot>
            </tfoot>
            <tbody>
                <?php 
                $visibleCols = array_keys((array)$this->plugin->data['head']); $i=0;
                foreach($this->plugin->data['list'] as $row) { ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <?php 
                    foreach($row as $key => $item) { 
                        if (in_array($key, $visibleCols)) { ?>
                    <td>
                        <?php echo JText::_($item); ?>
                    </td>    
                    <?php } } ?>
                </tr>
                <?php $i++; } ?>
            </tbody>
        </table>

        <br/>
        
        <div id="overwrite-selector" class="nofloat">
            <input type="checkbox" id="import-overwrite" name="import_overwrite" value="yes">
            <div style="margin:3px; float: left;"><?php  echo JText::_('COM_NEWSLETTER_IMPORT_OVERWRITE'); ?></div>
        </div>
        
        <div class="form-actions">
            <div class="fltrt">
                <input 
                    type="button" 
                    class="button plugin-icon" 
                    role="formSubmit" 
                    value="<?php echo JText::_('PLG_MIGUR_IMPORTEXAMPLE_IMPORT'); ?>" />

                <input 
                    type="button" 
                    class="button plugin-icon" 
                    role="formCancel" 
                    value="<?php echo JText::_('JCANCEL'); ?>" />
            </div>
        </div>   
        
        <input type="hidden" name="pluginevent" value="onMigurImportExecRule" />
        <input type="hidden" name="pluginname" value="<?php echo $this->plugin->name; ?>" />

        <input type="hidden" name="option" value="com_newsletter" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="task" value="list.importPluginTrigger" />
        <input type="hidden" name="format" value="html" />
        <input type="hidden" name="list_id" value="<?php echo $this->listId; ?>" />
        <?php JHtml::_('form.token'); ?>
    </form>    
    
    <div class="plugin-description">
        <?php echo JText::_($this->plugin->description); ?>
    </div>
    
</fieldset>