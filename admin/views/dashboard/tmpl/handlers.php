
<div class="container" id="container-left-left">
    <fieldset id="dashboard-newsletters">
        <legend><?php echo JText::_('COM_NEWSLETTER_NEWSLETTERS'); ?></legend>
        <?php echo MigurToolBar::getInstance('newsletters-toolbar')->render(); ?>
    </fieldset>
    <fieldset id="dashboard-config">
        <legend><?php echo JText::_('COM_NEWSLETTER_CONFIG_N_INSTALL'); ?></legend>
        <?php echo MigurToolBar::getInstance('config-toolbar')->render(); ?>
    </fieldset>
</div>

<div class="container" id="container-left-right">
    <fieldset id="dashboard-subscribers">
        <legend><?php echo JText::_('COM_NEWSLETTER_SUBSCRIBERS'); ?></legend>
        <?php echo MigurToolBar::getInstance('subscribers-toolbar')->render(); ?>
    </fieldset>
    <fieldset id="dashboard-help">
        <legend><?php echo JText::_('COM_NEWSLETTER_HELP_N_ABOUT'); ?></legend>
        <?php echo MigurToolBar::getInstance('help-toolbar')->render(); ?>
    </fieldset>
</div>

<fieldset id="statistic">
	<legend><?php echo JText::_('COM_NEWSLETTER_STATISTICS'); ?></legend>
	<div id="dashboard-statistic-chart"></div>
</fieldset>

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
