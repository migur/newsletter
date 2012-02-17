/**
 * The javascript file for conflicts view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
    try {
		
		if ($('conflict-resolver-link')) {
			
			$('conflict-resolver-link').addEvent('click', function(ev){

				ev.stop();

				SqueezeBox.open(url, {
					handler: 'iframe',
					size: {x: 900, y: 600}
				});
			});
		}	

    } catch(e) {
        if (console && console.conflict) console.conflict(e);
    }
});
