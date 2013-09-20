/**
 * The javascript file for smtpprofile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {

	// Fix for IE8. Because IE triggers native submit when
	// clicking on <button> that is placed INSIDE of a form.
	// So we need to prevent that default unwanted action.
	$$('form button').each(function(el){
		var onClick = el.getProperty('onclick');
		if (onClick) el.setProperty('onclick', 'event.returnValue = false; ' + onClick + '; return false;');
	})


    $('smtp-toolbar-cancel')
        .addEvent('click', function(ev){
			ev.stop();
			Migur.closeModal();
    });


	// Check button. Do a check.
    $('smtp-toolbar-publish')
        .addEvent('click', function(ev){

			ev.stop();

			var inputs  = $('adminForm').toQueryString();
			var obj = new Hash(inputs.parseQueryString());
			obj['task'] = 'smtpprofile.checkconnection';

			// Resotore preloader

			new Request({
				url: '?option=com_newsletter',
				data: obj,

				onComplete: function(res){

					$('preloader-container').removeClass('preloader');

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

			$('preloader-container').addClass('preloader');

			return false;
    });


	if (migurIsJoomlaProfile == true) {
		Array.each($$('.element-standard') , function(el){
			el.setProperty('readonly', 'readonly');
			el.setStyle('color', '#ccc');
		});
	}

});
