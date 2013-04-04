/**
 * The javascript file for subscriber view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function(){

	var isNew = $$('[name="subscriber_id"]')[0].getProperty('value')  == '';

	if (isNew) {
		$$('#toolbar-cancel button')[0]
			.removeProperty('onclick')
			.addEvent('click', function(ev){
				ev.stop();
				Migur.closeModal();
			})
			
	} else {


		if ( $('history-list') ) {
			Migur.lists.sortable.setup($('history-list'));
			historyPaginator = new Migur.lists.paginator($('history-list'));
		}

		Migur.lists.sortable.setup($('table-subs'));
	}
	
});
