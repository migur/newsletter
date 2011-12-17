window.addEvent('domready', function() {
	try {
	
		$$('.test-title').addEvent('click', function(){

			var row = $(this).getParent('tr');
			var url = row.getElements('.test-url')[0].getProperty('value');
			var method = row.getElements('.test-method')[0].getProperty('value');
			var params = row.getElements('.test-params')[0].getProperty('value');
			var other = row.getElements('.test-other')[0].getProperty('value');
			
			//console.log(url, method, params, other);
			var data = params + ((other != '')? '&'+other : '');
			
			data = (data != '')? data.parseQueryString() : {};

			$('test-result').addClass('preloader');

			new Request({
				url: migurSiteRoot + url,
				method: method,
				data: data,
				
				onComplete: function(res){
					$('test-result').removeClass('preloader');
					$('test-result').set('html', res);
				},
				
				onError: function(res){
					$('test-result').removeClass('preloader');
					$('test-result').set('html', '<ERROR>'+res);
				}
				
			}).send();

		});
	
	
	
	
	
	} catch(e){}

});