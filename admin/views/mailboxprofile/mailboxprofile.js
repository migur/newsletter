/**
 * The javascript file for smtpprofile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function(){

    $('mailbox-toolbar-cancel')
        .addEvent('click', function(ev){
			ev.stop();
			Migur.closeModal();
    });

    $('mailbox-toolbar-publish')
        .addEvent('click', function(ev){

			ev.stop();

			var inputs  = $('mailboxprofile-form').toQueryString();
			var obj = new Hash(inputs.parseQueryString());
			obj['task'] = 'mailboxprofile.checkconnection';

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
	
});
