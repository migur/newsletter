/**
 * The javascript file for logs view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
    try {

		$$('table.logslist label.control-details').addEvent('click', function(){
			
			var id = $(this).getParent('.item').getElements('[name=cid[]]')[0].getProperty('value');

			Migur.modal.show('#modal-details', { 'href': migurSiteRoot + 'administrator/index.php?option=com_newsletter&view=log&tmpl=component&log_id='+id } )
		});

    } catch(e) {
        if (console && console.log) console.log(e);
    }
});
