/**
 * The javascript file for automailings view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
try {

	$$('#automailings-edit .toolbar, #automailings-trash .toolbar').each(function(el) { el.removeProperty('onclick'); });
	
	$$('.automailingslist [type=checkbox]').addEvent('click', function(){

       var active = $$('.automailingslist [type=checkbox]').some(function(el){
           return el.get('checked');
       });

       if( active ) {
            $$('#automailings-edit, #automailings-trash').each(function(el){
	            el.getElements('span')[0].removeClass('toolbar-inactive');
			});
			
        } else {
            $$('#automailings-edit, #automailings-trash').each(function(el){
                el.getElements('span')[0].addClass('toolbar-inactive');
			});
       }
   });

/* Expand the functionality of the delete button */
$$('#automailings-trash .toolbar')[0].addEvent('click', function(el){
	if ($(this).getElements('span')[0].hasClass('toolbar-inactive')) return false;
	
	if( confirm(Joomla.JText._('COM_NEWSLETTER_ARE_YOU_SURE_QM', 'Are you sure?')) ) {
		Joomla.submitform('automailings.delete', $$('[name=automailingsForm]')[0]);
	};
	return false;
});

/* Expand the functionality of the edit button */
$$('#automailings-edit a')[0].addEvent('click', function(ev){

	ev.stop();
	
	if ($$('[name=cid[]]').length > 0) {
		$$('[name=cid[]]').each(function(el){
			if(el.getProperty('checked')) {
				el.getParent('tr').getElements('.modal').fireEvent('click');
			}
		});
	}	
});


$$('.automailingslist [type=checkbox]')[0].fireEvent('click');

if ($$('.automailingslist .search').length > 0) {
	
	$$('.automailingslist .search').addEvent('click', function(ev){

		if (ev) ev.stop();

		var id = $(this).getParent('tr').getElements('[name=cid[]]')[0].get('value');
		$('preview-container').setProperty('src', '?option=com_newsletter&layout=details&view=automailing&tmpl=component&automailing_id='+id);
		
		return false;
	});

	$$('.automailingslist .search')[0].fireEvent('click');
}


} catch(e){
    if (console && console.log) console.log(e);
}


});
