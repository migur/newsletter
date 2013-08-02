
window.addEvent('domready', function() {
try {	

	// Fix for IE8. Because IE triggers native submit when 
	// clicking on <button> that is placed INSIDE of a form.
	// So we need to prevent that default unwanted action.
	$$('form button').each(function(el){
	var onClick = el.getProperty('onclick');
	if (onClick) el.setProperty('onclick', 'event.returnValue = false; ' + onClick + '; return false;');
	})

	$$('#amitem-cancel button')
        .addEvent('click', function(ev){
			ev.stop();
			Migur.closeModal()
        });
		
		
	$('jform_time_start_img').addEvent('click', function(){
		$$('.calendar')[0].set('top', 0);
	});	
	
} catch(e) {}	
});