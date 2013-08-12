    <div id="tab-container-ovirview">
        <div id="overview1" class="form-text">
            <dl>
                <dt>
                    <?php echo $this->listForm->getLabel('name'); ?>
                </dt>
                <dd>
                    <?php echo $this->listForm->getInput('name'); ?>
                </dd>
                <dt>
                    <?php echo $this->listForm->getLabel('description'); ?>
                </dt>
                <dd>
                    <?php echo $this->listForm->getInput('description'); ?>
                </dd>
                <dt>
					<?php echo JHtml::_('migurhelp.link', 'smtpp', 'general', 'smtpp-list'); ?>
                    <?php echo $this->listForm->getLabel('smtp_profile_id'); ?>
                </dt>
                <dd>
                    <?php echo $this->listForm->getInput('smtp_profile_id'); ?>
                </dd>
                <dt></dt>

<?php if (!$this->isNew): ?>
                <dd id="chart-new-subs">

<script type="text/javascript">

	migurLegend = [
		"<?php echo JText::_("COM_NEWSLETTER_NO_OF_NEW_SUBSCRIBERS"); ?>"
	];

	migurData = { subs:[], labels:[] };
	Object.each(newSubsPerDay, function(item, key){
		migurData.subs.push(item);
		migurData.labels.push(key);
	});
	var pdRaph = Migur.chart.line("chart-new-subs", migurData.labels, [ migurData.subs ], 300, 100, migurLegend);

</script>
</dd>
<?php endif; ?>
            </dl>
        </div>
		
<?php if (!$this->isNew): ?>

        <div id="overview2" class="form-text">

            <dl>
                    <dt>Total sent and bounced</dt>
                    <dd id="newsletters_total_sent"></dd>
                    <dt>Opened newsletters</dt>
                    <dd id="newsletters_opened"></dd>
                    <dt>Active subscribers</dt>
                    <dd id="newsletters_active_subscribers"></dd>
            </dl>
        </div>

<script type="text/javascript">
	/* TODO: The bounces will be back in future versions */
	var legend = {
		data:[["## Sent mails"/*, "## Soft bounce", "## Hard bounce"*/]],
		position: 'south' }
	data = [ statTotalSent.total/*, statTotalSent.soft, statTotalSent.hard */];
	//data = [ 3,2,1 ];
	var tsPie = Migur.chart.pie("newsletters_total_sent", legend, data, 50, 50, 45);

	var legend = {
		data:[["## Not bounced", "## Opened"]],
		position: 'south' }
	data = [ statOpenedCount.total, statOpenedCount.opened ];
	//data = [ 0,1 ];
	var tsPie = Migur.chart.pie("newsletters_opened", legend, data, 50, 50, 45);

	var legend = {
		data:[["## Opened letters", "## Active subscribers"]],
		position: 'south' }
	data = [ statActiveSubscribersCount.newsletters, statActiveSubscribersCount.subscribers ];
	//data = [ 1,0,0 ];
	var tsPie = Migur.chart.pie("newsletters_active_subscribers", legend, data, 50, 50, 45);

        </script>

<?php endif; ?>

    </div>
