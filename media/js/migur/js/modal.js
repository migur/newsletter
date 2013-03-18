
window.addEvent('domready', function() {
	
	// We need jQuery and bootstrap's modal here.
	(function($){
		$('[data-toggle="migurmodal"]').bind('click', function(ev){
			ev.preventDefault();
			var popup = $(this).attr('data-target');
			var href = $(this).attr('href');
			var body = $(popup).find('.modal-body');
			
			body
				.html('<iframe class="modal-iframe" src="'+href+'" onLoad="jQuery(this).parent().removeClass(\'preloader\')"></iframe>')
				.addClass('preloader');
				
			jQuery(popup).modal('show');
		})
	})(jQuery)	

});
