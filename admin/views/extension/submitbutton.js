/**
 * The main submit functionality for forms in extension view.
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
Joomla.submitbutton = function(task)
{
        if (typeof (tinyMCE) != 'undefined') {
            $$('form')[0].getElements('textarea').each(function(el){
                var mce = tinyMCE.get(el.getProperty('id'));
                if ( mce && !mce.isHidden() ) {
                    mce.save();
                }
            });
        }

	if (task == '')
	{
		return false;
	}
	else
	{
		var isValid=true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close')
		{
			var forms = $$('form.form-validate');
			for (var i=0;i<forms.length;i++)
			{
				if (!document.formvalidator.isValid(forms[i]))
				{
					isValid = false;
					break;
				}
			}
		}

		if (isValid)
		{
            var inputs = $$('form')[0].toQueryString();
            var obj = inputs.parseQueryString();
            var href = 0; //document.location.href;
            var dialog = window.parent.Migur.moodialogs[href];

			console.log(obj);
            Object.each(obj, function(val, name){

                // if it is the params field
                var regex=/^jform\[params\]\[([^\[\]]+)\](\[\])?/;
                var res = regex.exec(name);
                var def = null;

                if (res && res[1]) {
                    var isArray = (res[2] && res[2] == '[]')? true : false;
                    if (val.length == 0) {
                        val = isArray? [] : '';
                    } else {
                        val = (isArray && typeof val == 'string')? [val] : val;
                    }
                    dialog.data.params[res[1]] = val;
                    return;
                }

                // if this is non-params field
                regex=/^jform\[([^\[\]]+)\](\[\])?/;
                res = regex.exec(name);
                if (res && res[1]) {
                    var isArray = (res[2] && res[2] == '[]');
                    if (val.length == 0) {
                        val = isArray? [] : '';
                    } else {
                        val = isArray? (typeof val == 'string') [val] : val;
                    }
                    dialog.data[res[1]] = val;
                }

            });

            dialog.task = task;

            dialog.close();
			return true;
		}
		else
		{
			//alert(Joomla.JText._('COM_NEWSLETTER_ERROR_UNACCEPTABLE','Some values are unacceptable'));
			return false;
		}
	}
}
