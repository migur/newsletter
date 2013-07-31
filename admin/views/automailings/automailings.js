/**
 * The javascript file for automailings view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
/**
 * The javascript file for templates view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', 

	function() {
	
		var glasses = $$('.automailingslist .search')
		
		// Add AJAX preview functionality to glasses
		glasses.addEvent('click', function(){

			var id = $(this).getParent('tr').getElements('[name=cid[]]')[0].get('value');
			$('preview-container').setProperty('src', '?option=com_newsletter&layout=details&view=automailing&tmpl=component&automailing_id='+id);

			return false;
		});

		glasses[0].fireEvent('click');

	}
);
