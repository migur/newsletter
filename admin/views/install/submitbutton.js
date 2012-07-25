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
			if (
				task == 'install.remove' &&
				!confirm(Joomla.JText._('ARE_YOU_REALY_WANT_TO_DELETE_THESE_EXTENSIONS_QM', 'Are you really want to delete these extensions'))
			) {
					return false;
			}

			if (!form) {
				form = document.subscriberForm
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

} catch(e){
    if (console && console.log) console.log(e);
} });
