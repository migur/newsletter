/**
 * The javascript file for logs view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
    try {

		$$('table.logslist label.search').addEvent('click', function(){
			
			var id = $(this).getParent('.item').getElements('[name=cid[]]')[0].getProperty('value');
			var href = "index.php?option=com_newsletter&view=log&tmpl=component&log_id="+id;

			SqueezeBox.open(href, {
				handler: 'iframe',
				size: {
					x: 700,
					y: 700
				}
			});
			
		});

    } catch(e) {
        if (console && console.log) console.log(e);
    }
});
