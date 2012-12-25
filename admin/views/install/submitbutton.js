/**
 * The main submit functionality for forms in subscriber view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * The implementation of main submit handler for the form.
 *
 * @return   void
 * @since    1.0
 * @modified Andrey
 */

window.addEvent('domready', function() { try {
		
	Joomla.submitbutton = function(task, form, element)
	{
		if (task == '') return false;

		switch(task) {
			case 'install.remove': form = document.adminForm; break;
			case 'install.restore': form = document.installForm; break;
			default: form = document.adminForm; break;
		}

			if (
				task == 'install.remove' &&
				!confirm(Joomla.JText._('ARE_YOU_REALY_WANT_TO_DELETE_THESE_EXTENSIONS_QM', 'Are you really want to delete these extensions'))
			) {
					return false;
			}

			Joomla.submitform(task, form);
			return false;
	}

} catch(e){
    if (console && console.log) console.log(e);
} });
