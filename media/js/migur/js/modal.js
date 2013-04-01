
window.addEvent('domready', function() {
	
	// We need jQuery and bootstrap's modal here.
	(function($, Migur){
		
		Migur.modal = {
			show: function(target, options) {
				
				if (options.type && options.type == 'iframe') {
					
					var body = $(target).find('.modal-body');

					body
						.html('<iframe class="modal-iframe" src="'+options.href+'" onLoad="jQuery(this).parent().removeClass(\'preloader\')"></iframe>')
						.addClass('preloader');

					jQuery(target).modal('show');
				}
			}
		}
		
		
		$('[data-toggle="migurmodal"]').bind('click', function(ev){
			
			ev.preventDefault();
			
			if ($(this).hasClass('disabled')) return;
			
			var popup = $(this).attr('data-target');
			var href = $(this).attr('href') || '#';
			var type = $(this).attr('data-type') || 'iframe';
			
			Migur.modal.show(popup, { 
				'type' : type,
				'href' : href
			});
		})
		
	})(jQuery, Migur)	

});
