/**
 * The javascript file for smtpprofile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {

    $$('#smtp-profile-cancel a')
        .removeProperty('onclick')
        .addEvent('click', function(){
            if (window && window.parent && window.parent.SqueezeBox) {
                window.parent.SqueezeBox.close();
            }
            return false;
    });


	// Check button. Do a check.
    $$('#smtp-profile-publish a')
        .removeProperty('onclick')
        .addEvent('click', function(){

			var inputs  = $('adminForm').toQueryString();
			var obj = new Hash(inputs.parseQueryString());
			obj['task'] = 'smtpprofile.checkconnection';

			// Resotore preloader
			if ($$('#smtp-profile .preloader').length > 0) {
				$$('#smtp-profile .preloader')[0].destroy();
			}
			$$('#smtp-profile ul')[0].grab(new Element('li', {
				'class': 'preloader'
			}), 'top');

			new Request({
				url: '?option=com_newsletter',
				data: obj,

				onComplete: function(res){

					// Hide preloader
					if ($$('#smtp-profile .preloader').length > 0) {
						$$('#smtp-profile .preloader')[0].destroy();
					}

					var response = new Migur.jsonResponseParser();

					response.setResponse(res);

					if (response.isError()) {
						alert(response.getMessagesAsList(Joomla.JText._('CONNECTION_FAILED','Connection failed!')));
					} else {
						alert(response.getMessagesAsList(Joomla.JText._('CONNECTION_OK', 'Connection ok!')));
					}

					return;
				}
			}).send();
				return false;
    });


	if (migurIsJoomlaProfile == true) {
		Array.each($$('.element-standard') , function(el){
			el.setProperty('readonly', 'readonly');
			el.setStyle('color', '#ccc');
		});
	}
});
