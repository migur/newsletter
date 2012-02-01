/**
 * The javascript file for smtpprofile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() { try {

    $$('#mailbox-toolbar-cancel a')[0]
        .removeProperty('onclick')
        .addEvent('click', function(){
            if (window && window.parent && window.parent.SqueezeBox) {
                window.parent.SqueezeBox.close();
            }
            return false;
    });

    $$('#mailbox-toolbar-publish a')[0]
        .removeProperty('onclick')
        .addEvent('click', function(){

			var inputs  = $('mailboxprofile-form').toQueryString();
			var obj = new Hash(inputs.parseQueryString());
			obj['task'] = 'mailboxprofile.checkconnection';

			// Resotore preloader
			if ($$('#mailbox-toolbar .preloader').length > 0) {
				$$('#mailbox-toolbar .preloader')[0].destroy();
			}	
			$$('#mailbox-toolbar ul')[0].grab(new Element('li', {
				'class': 'preloader' 
			}), 'top');

			new Request({
				url: '?option=com_newsletter',
				data: obj,

				onComplete: function(res){

					// Hide preloader
					if ($$('#mailbox-toolbar .preloader').length > 0) {
						$$('#mailbox-toolbar .preloader')[0].destroy();
					}	
					
					try { res = JSON.decode(res); }
					catch (e) { res = false; }
					
					if (res && res.status == 'ok') {
						alert(Joomla.JText._('CONNECTION_OK', 'Connection ok!'));
						return;
					}
					
					var text = Joomla.JText._('CONNECTION_FAILED','Connection failed!');
					
					if(res.status) {
						text += "\n" + res.status;
					}
						
					alert(text);
					return;
					
				}
			}).send();
				return false;
    });
	


} catch(e){
    if (console && console.log) console.log(e);
} });
