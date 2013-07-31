Migur.chart.pie = function(containerId, legend, data, x, y, r) {

        var colorsList = [
			'#1751a7',
			'#8aa717',
			'#308236',
			'#876F26',
			'#F5BC11',
			'#CAE324'
		];


        // if all data items == 0 then set only one to 100 to draw circle
        var empty = true;
        data.each(function(el) {
            if (el != 0) empty = false;});

        if (!empty) {
            var offset = y * 2 + 10;
        } else {
            r = 1;
            offset = 10;
        }

		$(containerId).setStyle('width', x * 2 + 50);
		$(containerId).setStyle('height', offset + data.length * 20);
		var raphael = Raphael(containerId);
		raphael.g.txtattr.font = "12px 'Fontin Sans', Fontin-Sans, sans-serif";

        // The g.piechart modifies data in some cases, so we have to prevent it.
        var dt = data.clone();//return;
        var pie = raphael.g.piechart(
            x, y, r,
            dt,
            {
                colors: colorsList
            }
        );
            
        pie.hover(function () {
            this.sector.stop();
            this.sector.scale(1.1, 1.1, this.cx, this.cy);
            if (this.label) {
                this.label[0].stop();
                this.label[0].scale(1.5);
                this.label[1].attr({"font-weight": 800});
            }
        }, function () {
            this.sector.animate({scale: [1, 1, this.cx, this.cy]}, 500, "bounce");
            if (this.label) {
                this.label[0].animate({scale: 1}, 500, "bounce");
                this.label[1].attr({"font-weight": 400});
            }
        });

		// Labels
		if (typeof(legend.data[0]) != 'object') {
			legend.data = [legend.data];
		}
		var pdLabels = legend.data[0];
		var x = 15;var h = 18;

		pie.labels = raphael.set();

		for( var i = 0; i < pdLabels.length; ++i ) {
			var clr = colorsList[i];

			var mark = raphael.g["disc"](x + 5, h * i + offset, 5)
			.attr({fill: clr, stroke: "none"});
			var txt = raphael.text(x + 20, h * i + offset, pdLabels[i].replace('##', data[i]))
			.attr(raphael.g.txtattr)
			.attr({fill: "#000", "text-anchor": "start"});

			if (typeof(legend.data[1]) != 'undefined') {
				
				var txt2 = raphael.text(x + 140, h * i + offset, legend.data[1][i])
				.attr(raphael.g.txtattr)
				.attr({fill: "#888", "text-anchor": "start", "font-weight": "bold"});
			}	

			pie.labels.push(raphael.set());
            pie.labels[i].push(mark);
            pie.labels[i].push(txt);
//		    pie.push(pie.labels);
//			pie.covers[i].label = pie.labels[i]
		};
	}

