<div id="newsletters_statistics">
	<dl>
		<dt><b><?php echo JText::_('COM_NEWSLETTER_TOTAL_SENT_AND_BOUNCED'); ?></b></dt>
		<dd id="newsletters_total_sent"></dd>
		<dt><b><?php echo JText::_('COM_NEWSLETTER_OPENED_NEWSLETTERS'); ?></b></dt>
		<dd id="newsletters_opened"></dd>
		<dt><b><?php echo JText::_('COM_NEWSLETTER_ACTIVE_SUBSCRIBERS'); ?></b></dt>
		<dd id="newsletters_active_subscribers"></dd>  
	</dl>

        <div id="holder"></div>

</div>
<div class="clr"></div>
<script text="javascript">

	/* TODO: The bounces will be back in future versions */
	var legend = {
		data:[["## Sent mails"/*, "## Soft bounce", "## Hard bounce"*/]],
		position: 'south' }
	data = [ statTotalSent.total/*, statTotalSent.soft, statTotalSent.hard*/ ];
	//data = [ 3,2,1 ];
	var tsPie = Migur.chart.pie("newsletters_total_sent", legend, data, 100, 100, 80);


	var legend = {
		data:[["## Not bounced", "## Opened"]],
		position: 'south' }
	data = [ statTotalSent.no, statOpenedCount.opened ];
	//data = [ 0,1 ];
	var tsPie = Migur.chart.pie("newsletters_opened", legend, data, 100, 100, 80);


	var legend = {
		data:[["## Opened letters", "## Active subscribers"]],
		position: 'south' }
	data = [ statActiveSubscribersCount.newsletters, statActiveSubscribersCount.subscribers ];
	//data = [ 1,0 ];
	var tsPie = Migur.chart.pie("newsletters_active_subscribers", legend, data, 100, 100, 80);

</script>