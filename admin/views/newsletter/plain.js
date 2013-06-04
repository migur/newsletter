
Migur.define("plain", function(){

		/* "Plain text" tab -> "Dynamic data" -> click-handlers */
		//el.setProperty('href', el.getProperty('rel'));
        $$('#dynamic-data-container [data-control="placeholder"]').addEvent('click', function(ev){
			
				ev.stop();
				
                $('jform_plain').insertAtCursor($(this).getProperty('data-value'), false);
				
                return false;
        });

});
