/**
 * The javascript file for import view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
try {

	$$('[name="submit"]').addEvent('click', function(ev){
		
		ev.stop();
		
		var form = $('importForm');

		var importMan = new Migur.iterativeAjax({
			
			url: form.getProperty('action'),
			
			data: form.toQueryString().parseQueryString(),
			
			preloaderPath: '#import-preloader',
			
			messagePath: '#import-message',
			
			onComplete: function(messages, data){
					
				this.showAlert(
					messages,
					Joomla.JText._('TOTAL','Total')+": " + data.total
				);
			}

		});
			
		importMan.start();
	});
    
} catch(e){
    if (console && console.log) console.log(e);
}
});
