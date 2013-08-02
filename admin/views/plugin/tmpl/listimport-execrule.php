<div class="plugin-container plugin-import">
    <div class="legend"><?php echo JText::_($this->plugin->title); ?></div>
	
    <table class="adminlist  table table-striped">
        <tbody>
            <tr>
                <td width="50%"><?php echo JText::_("COM_NEWSLETTER_SUBSCRIBERS_ADDED"); ?></td>
                <td><?php echo $this->escape($this->plugin->data['added']); ?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_NEWSLETTER_SUBSCRIBERS_UPDATED"); ?></td>
                <td><?php echo $this->escape($this->plugin->data['updated']); ?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_NEWSLETTER_SUBSCRIBERS_ASSIGNED"); ?></td>
                <td><?php echo $this->escape($this->plugin->data['assigned']); ?></td>
            </tr>
            
            <?php if ($this->plugin->data['skipped'] > 0) { ?>
            <tr>
                <td><span style="color:red"><?php echo JText::_("COM_NEWSLETTER_SUBSCRIBERS_SKIPPED"); ?></span></td>
                <td><span style="color:red"><?php echo $this->escape($this->plugin->data['skipped']); ?></span></td>
            </tr>
            <?php } ?>
            
        </tbody>    
    </table>    
    <form id="plugin-form" action="<?php echo JUri::current(); ?>">
		
		<div class="form-actions">
			<input 
				type="button" 
				class="button plugin-icon" 
				data-role="formCancel" 
				value="<?php echo JText::_('COM_NEWSLETTER_CLOSE'); ?>" 
				onclick="migurPluginManager.cancel(); return false;"
			/>
		</div>
			
        <input type="hidden" name="pluginevent" value="onMigurImportExecRule" />
        <input type="hidden" name="pluginname" value="<?php echo $this->plugin->name; ?>" />
        <input type="hidden" name="option" value="com_newsletter" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="task" value="plugin.triggerListimport" />
        <input type="hidden" name="format" value="html" />
        <input type="hidden" name="list_id" value="<?php echo $this->listId; ?>" />
        <?php JHtml::_('form.token'); ?>
    </form>    
    
    <div class="plugin-description">
        <?php echo JText::_($this->plugin->description); ?>
    </div>
    
</div>