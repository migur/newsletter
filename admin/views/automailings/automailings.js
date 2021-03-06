/**
 * The javascript file for automailings view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
try {

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
