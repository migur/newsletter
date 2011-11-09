/**
 * The main submit functionality for forms in mailboxprofile view.
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
        if (!form) {
            form = document.mailboxprofileForm
        }

	if (task == '')
	{
		return false;
	}
	else
        {
                var action = task.split('.');
                if ( document.formvalidator.isValid(form) ||
                     action[1] == 'cancel' ||
                     action[1] == 'close' ) {
                     
			Joomla.submitform(task, form);
			return true;
		}
		else
		{
			//alert(Joomla.JText._('COM_NEWSLETTER_ERROR_UNACCEPTABLE','Some values are unacceptable'));
			return false;
		}
	}
}
