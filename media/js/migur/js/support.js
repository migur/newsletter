window.addEvent('domready', function() {
	try {

		$$('[role="support"]').addEvent('click', function(event) {
			
			event.stop();

			// Get namespace and its data
			var url = $(this).getProperty('resource');
			
			window.open(url, '_blank');
			
			// Enjoy!
		});

} catch(e){
    if (console && console.log) console.log(e);
}
});
		
