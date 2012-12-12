<div id="stat-letters">
	<dl>
		<dt><b><?php echo JText::_('COM_NEWSLETTER_OPENED_NEWSLETTERS'); ?></b></dt>
		<dd id="stat-opened"></dd>
		<dt><b><?php echo JText::_('COM_NEWSLETTER_ACTIVE_SUBSCRIBERS'); ?></b></dt>
		<dd id="stat-active-subscribers"></dd>
	</dl>

        <div id="holder"></div>

</div>


<script type="text/javascript">

	/* TODO: The bounces will be back in future versions */
//	var legend = { 
//		data:[ 
//				["## Sent mails"/*, "## Soft bounce", "## Hard bounce", "## Technical bounce"*/],
//				[''/*,(statTotalSent.total > 0)? parseInt((statTotalSent.soft / statTotalSent.total) * 100) + '%' : 0,
//					(statTotalSent.total > 0)? parseInt((statTotalSent.hard / statTotalSent.total) * 100) + '%' : 0,
//					(statTotalSent.total > 0)? parseInt((statTotalSent.technical / statTotalSent.total) * 100)  + '%' : 0*/ ]
//			],	
//		position: 'south'
//	}
//	var data = [ statTotalSent.total/*, statTotalSent.soft, statTotalSent.hard, statTotalSent.technical*/ ];
//	var tsPie = Migur.chart.pie("stat-total-sent", legend, data, 70, 70, 60);


	var legend = { 
		data:[ 
				["## Not bounced", "## Opened"],
				['', (statTotalSent.total > 0)? parseInt((statOpenedCount.opened / statTotalSent.no) * 100) + '%' : 0]
			],	
		position: 'south'
	}
	var data = [ statTotalSent.no, statOpenedCount.opened ];
	var opPie = Migur.chart.pie("stat-opened", legend, data, 70, 70, 60);


	var legend = { 
		data:[ ["## Opened newsletters", "## Active subscribers"] ],	
		position: 'south'
	}
	var data = [ statActiveCount.newsletters, statActiveCount.subscribers ];
	var opPie = Migur.chart.pie("stat-active-subscribers", legend, data, 70, 70, 60);
	
</script>
