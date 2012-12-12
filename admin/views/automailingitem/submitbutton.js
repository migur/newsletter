/**
 * The main submit functionality for forms in newsletter view.
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
Joomla.submitbutton = function(task, form)
{
	if (task == '')	{
		return false;
	}
	
	if (form == undefined) {
		form = $$('form.form-validate')[0];
	}
	
	return Joomla.submitform(task, form);
}
