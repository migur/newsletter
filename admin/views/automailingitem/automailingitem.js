
window.addEvent('domready', function() {
try {	
	
	$$('#amitem-cancel button')
        .removeProperty('onclick')
        .addEvent('click', function(){
			
            if (window && window.parent && window.parent.SqueezeBox) {
                window.parent.SqueezeBox.close();
            }
			
			if (window && window.parent && window.parent.jQuery && window.parent.jQuery('.modal.in')) {
				window.parent.jQuery('.modal.in').data('modal').hide();
			}
			
            return false;
        });
		
		
	$('jform_time_start_img').addEvent('click', function(){
		$$('.calendar')[0].set('top', 0);
	});	
	
} catch(e) {}	
});