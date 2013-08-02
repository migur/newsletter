<div id="stat-clicks">
	<dl>
		<dt><b>Sents, Clicks, Bounces, Views</b></dt>
		<dd><div id="all-in-one"></div></dd>
		
		<dt>
			<b>Number of clicks and views per day</b>
			<div class="fltrt">
				<div id="days-label"><?php echo JText::_('COM_NEWSLETTER_SELECT_RANGE'); ?></div>
				<select name="days">
					<?php echo JHtml::_('select.options', array(
						'5'  => '5 '   . JText::_('COM_NEWSLETTER_DAYS'),
						'10' => '10 '  . JText::_('COM_NEWSLETTER_DAYS'),
						'15' => '15 '  . JText::_('COM_NEWSLETTER_DAYS'),
						'30' => '30 '  . JText::_('COM_NEWSLETTER_DAYS'),
						'45' => '45 '  . JText::_('COM_NEWSLETTER_DAYS'),
						'60' => '60 '  . JText::_('COM_NEWSLETTER_DAYS'),
					), 'value', 'text', array($this->days)); ?>
				</select>
			</div>	
		</dt>
		<dd><div id="stat-clicks-per-day"></div></dd>
		
		<dt><b>Number of clicks and views per hour (last 24 hours)</b></dt>
		<dd>
			<div id="stat-clicks-per-hour"></div>
		</dd>
	</dl>

        <div id="holder"></div>

</div>

<script type="text/javascript">
	
	data.labels = [
		'Total sent', 
		'Total clicks', 
		'Bounced mails', 
		'Active subscribers',
		'Opened mails']
	
	data.views = [
		[
			statTotalSent.total,
			statTotalClicks.total,
			statTotalSent.hard + statTotalSent.soft + statTotalSent.technical,
			statActiveCount.subscribers,
			statActiveCount.newsletters
		]
	];
	
	var pdRaph = Migur.chart.hline.draw("all-in-one", data.labels, data.views, [], {
		chart: {
			width: 500,
			height: 110,
			padding: 10,
			graph: {
				// Wery important that this dimensions matches with 
				// NON-FRACTIONAL step of bars. Try several values to get the best result.
				width:  430,
				height: 100	}}
	});


	data = { views:[], clicks:[], labels:[]	};
	Object.each(clicksPerDay, function(item, key){
		data.clicks.push(item);
		data.labels.push(key);
		data.views.push(viewsPerDay[key]);
	});
	var pdRaph = Migur.chart.line("stat-clicks-per-day", data.labels, [ data.views, data.clicks ], 578, 100, []);



	dataHour = { views:[], clicks:[], labels:[]	};
	Object.each(clicksPerHour, function(item, key){
		dataHour.clicks.push(item);
		dataHour.labels.push(key.substr(11,2));
		dataHour.views.push(viewsPerHour[key]);
	});
	var phRaph = Migur.chart.line("stat-clicks-per-hour", dataHour.labels, [ dataHour.views, dataHour.clicks ], 578, 100, []);

</script>
