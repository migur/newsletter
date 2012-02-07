window.addEvent('domready', function() {
	try {
	
		$$('.test-title').addEvent('click', function(){

			var row = $(this).getParent('tr');
			var url = row.getElements('.test-url')[0].getProperty('value');
			var method = row.getElements('.test-method')[0].getProperty('value');
			var params = row.getElements('.test-params')[0].getProperty('value');
			var other = row.getElements('.test-other')[0].getProperty('value');
			
			var data = params + ((other != '')? '&'+other : '');
			
			data = (data != '')? data.parseQueryString() : {};

			$$('.test-result')[0].addClass('preloader');

			new Request({
				url: migurSiteRoot + url,
				method: method,
				data: data,
				
				onComplete: function(res){
					$$('.test-result')[0].removeClass('preloader');
					$$('.test-result')[0].set('html', res);
				},
				
				onError: function(res){
					$$('.test-result')[0].removeClass('preloader');
					$$('.test-result')[0].set('html', '<ERROR>'+res);
				}
				
			}).send();

		});
	
	
		$('result-type').addEvent('click', function(){
			
			var visible = $$('.test-result')[0];
			
			var nonVisible = (visible.hasClass('test-html'))? 
				 $$('.test-text')[0] : $$('.test-html')[0];
			
			visible.removeClass('test-result');
			visible.setStyle('display', 'none');
			
			nonVisible.addClass('test-result');
			nonVisible.setStyle('display', 'block');
			
		});
	
	
	} catch(e){}

});