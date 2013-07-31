Migur.chart.hline = {
	
	options: {},
	
	draw: function(containerId, xLabels, data, pdLabels, options) {
		
				this._setOptions(options);
				
				var o = this.options;
				var _this = this;
		
                $(containerId).setStyle('width', o.chart.width + 100);
                $(containerId).setStyle('height', o.chart.height);

                this.raphael = Raphael(containerId);
                var xData = [];
                var yData = [];

                if (data.length < 1) {
                    var text = Joomla.JText._('WRONG_CHART_DATA',"Wrong chart data.");

                    console.error(text);
                    return false;
                }

                data.each(function (dataValue, dataKey) {

                    dataValue.each(function (value, key) {
                        
                        if (typeof xData[dataKey] == 'undefined') {
                            xData[dataKey] = new Array();
                        }

                        if (typeof yData[dataKey] == 'undefined') {
                            yData[dataKey] = new Array();
                        }

                        yData[dataKey].push(value);
                        xData[dataKey].push(key + 0.5);
                    });
                });

                var lWidth = o.chart.width - 19;
                    
				this._drawLeftOyForBars(xLabels);
				
                if (yData[0]) {
					this._drawHChart(yData[0], 0);
					this._setHoversForBars(this.hbars);
                }
	},
	
	_drawLeftOyForBars: function(labels) {
		
		var o = this.options;
		var i;
		
		// Set width of left Oy
		for (i=0, max = 0; i < labels.length; i++) {
			if(max < labels[i].length) {
				max = labels[i].length;
			} 
		}
		o.chart.leftOy.width = (o.fontsize * 0.5) * max + o.chart.padding;

		// Let's create the Xaxis
		// create the fake array
		var fake = [' '];
		labels.each(function(){
			fake.push(' ');
		});

		// Draw the axis line without labels
//		var len = labels.length;
//		this.leftOy = this.raphael.g.axis(
//			o.chart.padding + o.chart.leftOy.width, 
//			o.chart.padding + o.chart.topOx.height + o.chart.graph.height, 
//			o.chart.graph.height,  
//			null,		
//			null, 
//			len, 
//			1, // orientation 
//			fake, 
//			't'
//		);
		
		// Print the labels
		var yStep = o.chart.graph.height / labels.length;
		for (i=0; i < labels.length; i++) {

			var label = this.raphael.text(
				o.chart.leftOy.width, 
				o.chart.padding + i * yStep + yStep / 2, 
				labels[i]);

			label.attr('font-weight', 'bold');
			label.attr('font-size', o.fontsize);
			label.attr('fill', '#888');
			label.attr('text-anchor', 'end');
		}
	},


	_drawBackground: function() {
		
		var o = this.options;
		
		this.background = this.raphael.rect(
			o.chart.padding + o.chart.leftOy.width,
			o.chart.padding + o.chart.topOx.height,
			o.chart.graph.width,
			o.chart.graph.height
		);

		this.background.attr({
			fill: "0-#deebff-#fff",
			'stroke-width': 0
			//'stroke': '#ccc'
		});
	},	


	_drawHChart: function(data, idx) {
		
		var o = this.options;
		var _this = this;
		
		if (o.chart.graph.width == null) {
			o.chart.graph.width  = o.chart.width  - (o.chart.padding * 2 + o.chart.leftOy.width + o.chart.rightOy.width);
		}
		
		if (o.chart.graph.height == null) {
			o.chart.graph.height = o.chart.height - (o.chart.padding * 2 + o.chart.topOx.height + o.chart.bottomOx.height);
		}	

		if (this.backgoriund == undefined) {
			this._drawBackground();
		}

		this.hbars = this.raphael.g.hbarchart(
			o.chart.padding + o.chart.leftOy.width,
			o.chart.padding + o.chart.topOx.height,
			o.chart.graph.width - o.chart.padding,
			o.chart.graph.height,
		   
			[data],
			{
				smooth: true,
				opacity: 50,
				colors: [o.colorsList[idx]],
				stretch: true,
				radius: 5,
				type: "soft"}
		);
			
			
		// Modify bars and draw labels for each bar	
		this.hbars.each(function(){
			
			this.bar.attr('stroke', '#fff');
			this.bar.attr('stroke-width', 1);
			
			if (this.bar.value > 0) {
				this.bar.label = _this.raphael.text(
					this.bar.x + 4, 
					this.bar.y, 
					this.bar.value);

				//label.rotate(45, true);
				this.bar.label.attr('font-weight', 'bold');
				this.bar.label.attr('font-size', o.fontsize);
				this.bar.label.attr('fill', '#008');
				this.bar.label.attr('text-anchor', 'start');
			}
			
		});
	},
	
	
	_setHoversForBars: function(bars) {
		
		var o = this.options;
		var _this = this;

		bars.hover(
			function() {
				if (this.bar.value > 0) {
					this.bar.animate({scale:1.05}, 200);
					this.bar.label.prevColor = this.bar.label.attr('fill');
					this.bar.label.prevX = this.bar.label.attr('x');
					this.bar.label.attr('x', this.bar.label.prevX + 10);
					this.bar.label.attr('fill', '#8aa717');
				}
			},
			function() {
				if (this.bar.value > 0) {
					this.bar.animate({scale:1}, 300);
					this.bar.label.attr('fill', this.bar.label.prevColor);
					this.bar.label.attr('x', this.bar.label.prevX);
				}
			}
		);
	},

	_drawBLegend: function(labels){
		
		var o = this.options;
		
		// Print legend
		if (labels.length < 1) {
			labels = [ Joomla.JText._('NUMBER_OF_CLICKS',"Number of clicks"), Joomla.JText._('NUMBER_OF_VIEWS',"Number of views") ];
		}
		var x = 15;
		var h = 20;
		var offset = o.chart.height + 90;
		var i;
		for(i = 0; i < labels.length; ++i) {
			var clr = o.colorsList[i];

			this.raphael.g["disc"](x + 5, h * i + offset, 5)
			.attr({fill: clr, stroke: "none"});

			this.raphael.text(x + 20, h * i + offset, labels[i])
			.attr(this.raphael.g.txtattr)
			.attr({fill: "#000", "text-anchor": "start"});
		};

		
	},


	_setOptions: function(options) {
		
		this.options = {};
		
		this.options = {
			
			fontsize: 11,
			
			colorsList: [
				'#1751a7',
				'#8aa717' ],
			
			chart: {
				padding: 10,
				
				graph: {
					width:  null,
					height: null
				},
				
				leftOy: {
					width: 0},

				rightOy: {
					width: 0},
				
				topOx: {
					height: 0},

				bottomOx: {
					height: 0},
			}	
		};
		
		this.options = Object.merge(this.options, options);
	}
}	


