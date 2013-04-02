
window.addEvent('domready', function() {
try {	
	
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