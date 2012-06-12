/**
 * The javascript file for import view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */


iterativeImport = {
	
	start: function(){
		$$('[name=offset]').set('value', '0');
		$$('[name=limit]').set('value', '1000');
		$('import-message').set('html', Joomla.JText._('IMPORTING', 'Importing') + '...');

		iterativeImport.step();
	},
	
	step: function(){

		var form = $('importForm');
		
		
		new Request({
			url: form.getProperty('action'),
			data: form.toQueryString(),
			onComplete: iterativeImport.onComplete
		}).send();
		
		$('import-preloader').addClass('preloader');
	},
	
	error: function(text) {
		
		$('import-message').set('html', '');
		
		alert(text)
	},

	finish: function(text, data) {

		$('import-message').set('html', '');

		Object.each(data, function(el, name){
			text += "\n" + name + ": " + el;
		});
		
		alert(text);
	},

	onComplete: function(result){

		$('import-preloader').removeClass('preloader');

		var parser = new Migur.jsonResponseParser();
		parser.setResponse(result);
		
		if (parser.isError()) {
			return iterativeImport.error(parser.getMessagesAsList('AN_UNKNOWN_ERROR_OCCURED'));
		}
		
		var data = parser.getData();
		
		$('import-message').set('html', data.total + ' ' + Joomla.JText._('ITEMS_PROCESSED', 'items processed') + '...');
		
		if (data.fetched > 0) {
			// Let server decide about offset 
			$$('[name=offset]').set('value', '');
			return iterativeImport.step();
		}

		return iterativeImport.finish(parser.getMessagesAsList(), {'Total': data.total});
	}
}

window.addEvent('domready', function() {
try {

	$$('[name="submit"]').addEvent('click', function(ev){
		
		ev.stop();
		iterativeImport.start();
	});
    
} catch(e){
    if (console && console.log) console.log(e);
}
});
