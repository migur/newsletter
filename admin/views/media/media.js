/**
 * The javascript file for newsletter view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */


window.addEvent('domready', function() {
try {

	$('insert-button').addEvent('click', function(){

			var topDoc = document;
			var topWin = window;
	
			var iframe = document.getElementById('imageframe');
			var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
			
			var folder = $(innerDoc).getElements('[name=folder]')[0].value;
			
			var itemsFrame = $(innerDoc.body).getElements('#folderframe')[0];
			var itemsDoc = itemsFrame.contentDocument || itemsFrame.contentWindow.document;

			var checkboxes = $(itemsDoc).getElements('.manager [name=rm[]]');

			var someChecked = false;

			Array.each(checkboxes, function(item){
				
				if(item.checked) {
					
					var fullname = (folder? folder+'/' : '') + item.value;
					topWin.parent.jInsertFieldValue(fullname, topWin.parent.migurFieldId);
					
					someChecked = true;
				}	
				
				return false
			});
			
			if (someChecked) {
				if (topWin && topWin.parent && topWin.parent.SqueezeBox) {
					topWin.parent.SqueezeBox.close();
				}

				if (topWin && topWin.parent && topWin.parent.jQuery && topWin.parent.jQuery('.modal.in')) {
					topWin.parent.jQuery('.modal.in').data('modal').hide();
				}
			}
	});

} catch(e){
    if (console && console.log) console.log(e);
}

});
