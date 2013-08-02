<div>
	<div class="legend"><?php echo JText::_('MOD_UPDATER_INFORMATION'); ?></div>
    <table class="adminlist  table table-striped">
        <tbody>
            <tr class="row0">
                <td><?php echo JText::_('MOD_UPDATER_SUPPORTED_VERSION'); ?></td>
                <td><?php echo JText::_($this->info->is_valid); ?></td>
            </tr>
            <tr class="row1">
                <td><?php echo JText::_('MOD_UPDATER_VERSION'); ?></td>
                <td><?php echo JText::_($this->info->current_version); ?></td>
            </tr>
            <tr class="row0">
                <td><?php echo JText::_('MOD_UPDATER_LATEST'); ?></td>
                <td id="updater-latestversion"><?php echo JText::_($this->info->latest_version); ?></td>
            </tr>
            <tr class="row1">
                <td><?php echo JText::_('MOD_UPDATER_COPYRIGHT'); ?></td>
                <td><?php echo JText::_($this->info->copyright); ?></td>
            </tr>
            <tr class="row0">
                <td><?php echo JText::_('MOD_UPDATER_LICENSE'); ?></td>
                <td><?php echo JText::_($this->info->license_key); ?></td>
            </tr>
            <tr class="row1">
                <td><?php echo JText::_('MOD_UPDATER_DOMAINCODE'); ?></td>
                <td><?php echo JText::_($this->info->domain); ?></td>
            </tr>
        </tbody>
    </table>
</div>