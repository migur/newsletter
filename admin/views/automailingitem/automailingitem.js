
window.addEvent('domready', function() {
try {	
	
	$$('#amitem-cancel a')
        .removeProperty('onclick')
        .addEvent('click', function(){
            if (window && window.parent && window.parent.SqueezeBox) {
                window.parent.SqueezeBox.close();
            }
            return false;
        });
		
		
	$('jform_time_start_img').addEvent('click', function(){
		$$('.calendar')[0].set('top', 0);
	});	
	
} catch(e) {}	
});