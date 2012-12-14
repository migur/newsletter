Migur.chart.line = function(containerId, xLabels, data, width, height, pdLabels) {
                var colorsList = [
                    '#1751a7',
                    '#8aa717'
                ];

                var colorIdx = 0;

                $(containerId).setStyle('width', width + 50);
                $(containerId).setStyle('height', height + 130);

                var raphael = Raphael(containerId);

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

                var lHeight = height - 15,
                    lWidth = width - 19,
                    lTop = 24,
                    lLeft = 24;

                var rect = raphael.rect(lLeft + 7, lTop, lWidth, lHeight + 2);
                rect.attr({
                    fill: "90-#deebff-#ffffff",
                    "stroke-width": 0
                });

                if (yData[1]) {
                    var bars = raphael.g.barchart(
                       28, 10, width - 16, height + 20,
                        [yData[1]],
                        {
                            smooth: true,
                            opacity: 50,
                            colors: [colorsList[colorIdx]],
                            stretch: true,
							type:'soft'
                        }
                    );

					bars.each(function(){
						this.bar.attr('stroke', '#fff');
						this.bar.attr('stroke-width', 1);
					});	

                    bars.hover(
                        function() {
                            if (this.bar.value > 0) {
                                this.bar.animate({scale:1.05}, 200);
                                this.bar.flag = raphael.g.tag(this.bar.x, this.bar.y - 10, this.bar.value, 0, 1)
                                .insertBefore(this.bar)
                                    .attr([{fill: '#888'}]);
                            }
                        },
                        function() {
                            if (this.bar.value > 0) {
                                this.bar.animate({scale:1}, 300);
                                this.bar.flag.animate({opacity: 0}, 200, function() {this.remove();});
                            }
                        }
                    );

                    colorIdx++;
                }



                if (yData[0]) {
                    var lines = raphael.g.linechart(
                       20, 20, width, height,
                        xData,
                        [yData[0]],
                        {
                            symbol: "o",
                            smooth: true,
                            colors: [colorsList[colorIdx]],
                            axis: "0 0 0 0"
                        }
                    );

                    lines.hover(
                        function() {
                            this.symbol.attr({r:5});
                            this.line.animate({"stroke-width": 3}, 200);
                            this.flag = raphael.g.tag(this.x, this.y, this.value, 160, 5)
                                .insertBefore(this)
                                    .attr([{fill: "#888"}]);
                        },
                        function() {
                            this.symbol.attr({r:3});
                            this.flag.animate({opacity: 0}, 300, function() {this.remove();});
                            this.line.animate({"stroke-width": 2}, 200);
                        }
                    );

                    lines.lines.attr({"stroke-width": 2});
                    lines.symbols.attr({"stroke": '#fff'});
                    lines.symbols.attr({r: 3});
                    colorIdx++;
                }

                // Let's create the Xaxis
                // create the fake array
                var fake = [' '];
                xData[0].each(function(){
                    fake.push(' ');
                });

                var len = xData[0].length;
                var xAxis = raphael.g.axis(30, height + 10, lWidth, 0, len, len, 0, fake, 't');

                // Print the labels
                var xOffset = (lWidth) / (xLabels.length);

                var sc = 10;
                var iter = sc;
                for (var i=0; i < xLabels.length; i++) {

                    if (iter >= sc) {

                        iter = xOffset;
                        var scale = xLabels[i].length / 2 * 4;
                        var label = raphael.text(30 + scale + xOffset * (i + 0.4), height + 20 + scale, xLabels[i]);
                        label.rotate(45, true);
                        label.attr('font-weight', 'bold');
                        label.attr('font-size', 11);
                        label.attr('fill', '#888');
                    } else {
                        iter += xOffset;
                    }
                }

                // Print legend
                if (pdLabels.length < 1) {
                    pdLabels = [ Joomla.JText._('NUMBER_OF_CLICKS',"Number of clicks"), Joomla.JText._('NUMBER_OF_VIEWS',"Number of views") ];
                }
                var x = 15;
                var h = 20;
                var offset = height + 90;
                var i;
                for(i = 0; i < pdLabels.length; ++i) {
                    var clr = colorsList[i];
                    
                    raphael.g["disc"](x + 20, h * i + offset, 5)
                    .attr({fill: clr, stroke: "none"});

                    raphael.text(x + 35, h * i + offset, pdLabels[i])
                    .attr(raphael.g.txtattr)
                    .attr({fill: "#000", "text-anchor": "start"});
                };
}


