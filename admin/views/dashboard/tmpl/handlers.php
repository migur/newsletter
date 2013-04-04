<div class="row-fluid">
	<div class="span6">
        <legend>Newsletters</legend>
		
		<a class="btn btn-success" href="index.php?option=com_newsletter&amp;view=newsletter">
			Create Newsletter
		</a>
		
		<a class="btn" rel="{handler: 'iframe', size: {x: 920, y: 450}, onClose: function() {}}" href="<?php echo JRoute::_('index.php?option=com_newsletter&view=sender', false); ?>" class="modal">
			<span>Send Newsletter</span>
		</a>
	</div> 

    <div class="span6">
        <legend>Subscribers</legend>
		
		<a 
			class="btn btn-success" 
			href="<?php echo JRoute::_('index.php?option=com_newsletter&task=subscriber.add&tmpl=component', false); ?>"
			data-toggle="migurmodal"
			data-target="#modal-subscriber"
		>	
			<?php echo JText::_('COM_NEWSLETTER_SUBSCRIBER_CREATE'); ?>
		</a>
		<a class="btn btn-success" href="<?php echo JRoute::_('index.php?option=com_newsletter&task=list.add', false); ?>">
			<?php echo JText::_('COM_NEWSLETTER_CREATE_LIST'); ?>
		</a>
	</div>	
	
</div>

<br/>
<br/>

<div class="row-fluid">

	<div class="span6">
		
		<legend>Configuration &amp; Installation</legend>
		
		<a class="btn" rel="{handler: 'iframe', size: {x: 350, y: 150}, onClose: function() {}}" href="<?php echo JRoute::_('index.php?option=com_newsletter&view=install'); ?>" class="modal">
			Install Extensions
		</a>
		
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_newsletter&view=configuration'); ?>">
			Configuration
		</a>
	</div>

	<div class="span6">
		<legend>Help &amp; About</legend>
		<a class="btn" target="_blank" href="http://migur.com/products/newsletter">
			About
		</a>

		<a class="btn" target="_blank" href="http://migur.com/support/documentation/newsletter">
			Help
		</a>
	</div>

</div>

<br/>
<br/>

<div id="statistic">
	<legend><?php echo JText::_('COM_NEWSLETTER_STATISTICS'); ?></legend>
	<div id="dashboard-statistic-chart"></div>
</div>

<script type="text/javascript">

	migurLegend = [
		"<?php echo JText::_('COM_NEWSLETTER_LINE_OPENED_MAILS'); ?>",
		"<?php echo JText::_('COM_NEWSLETTER_LINE_ACTIVE_SUBSCRIBERS'); ?>"
	];

	migurData = { subs:[], clicks:[], labels:[]	};
	Object.each(opensPerDay, function(item, key){
		migurData.clicks.push(item);
		migurData.labels.push(key);
		migurData.subs.push(subsPerDay[key]);
	});
	var pdRaph = Migur.chart.line("dashboard-statistic-chart", migurData.labels, [ migurData.subs, migurData.clicks ], 570, 100, migurLegend);
</script>


<!--<script type="text/javascript">

	var raph = Raphael("dashboard-statistic-chart");
	raph.g.txtattr.font = "12px 'Fontin Sans', Fontin-Sans, sans-serif";

	var chart = raph.g.linechart(20, 10, 570, 100,
		[ [10, 20, 30, 40, 100], [10, 20, 30, 40, 100] ],
		[ [10, 30, 20, 60, 10], [30, 20, 70, 10, 20] ],
		{
			nostroke: false,
			axis: "0 0 1 1",
			symbol: "",
			smooth: false
		}
	);

	var labels = [
		"<?php //echo JText::_('COM_NEWSLETTER_LINE_OPENED_MAILS'); ?>",
		"<?php //echo JText::_('COM_NEWSLETTER_LINE_ACTIVE_SUBSCRIBERS'); ?>"
	];

	chart.labels = raph.set();
	var x = 15; var h = 20; var offset = 140;
	for( var i = 0; i < labels.length; ++i ) {
		var clr = chart.lines[i].attr("stroke");
		chart.labels.push(raph.set());
		chart.labels[i].push(raph.g["disc"](x + 5, h * i + offset, 5)
		.attr({fill: clr, stroke: "none"}));

		chart.labels[i].push(txt = raph.text(x + 20, h * i + offset, labels[i])
		.attr(raph.g.txtattr)
		.attr({fill: "#000", "text-anchor": "start"}));
		//x += chart.labels[i].getBBox().width * 1.2;
	};
</script>-->
