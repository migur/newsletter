window.addEvent('domready', function() {
	try {

		$$('[role="support"]').addEvent('click', function(event) {
			
			event.stop();

			// Get namespace and its data
			var id = $(this).getProperty('rel');
			var parts = id.split('.');
			var data = Migur.app.support.resources[parts[0]][parts[1]];

			// Set defaults for absent options
			if (!data.options) data.options = {};
			var defaults = {width:600, height:600};
			var options = Object.merge(defaults, data.options);

			// Open a resource
			options = 'width:'+options.width+','+'height:'+options.height;
			
			data.windowPtr = window.open(
				data.url,
				'_blank'/*,
				options*/
			);
			
			// Enjoy!
		});

} catch(e){
    if (console && console.log) console.log(e);
}
});
		
