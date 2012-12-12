/**
 * The javascript file for smtpprofile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() { try {

    $$('#smtp-toolbar-cancel a')[0]
        .removeProperty('onclick')
        .addEvent('click', function(){
            if (window && window.parent && window.parent.SqueezeBox) {
                window.parent.SqueezeBox.close();
            }
            return false;
    });


	// Check button. Do a check.
    $$('#smtp-toolbar-publish a')[0]
        .removeProperty('onclick')
        .addEvent('click', function(){

			var inputs  = $('smtpprofile-form').toQueryString();
			var obj = new Hash(inputs.parseQueryString());
			obj['task'] = 'smtpprofile.checkconnection';

			// Resotore preloader
			if ($$('#smtp-toolbar .preloader').length > 0) {
				$$('#smtp-toolbar .preloader')[0].destroy();
			}	
			$$('#smtp-toolbar ul')[0].grab(new Element('li', {
				'class': 'preloader' 
			}), 'top');

			new Request({
				url: '?option=com_newsletter',
				data: obj,

				onComplete: function(res){

					// Hide preloader
					if ($$('#smtp-toolbar .preloader').length > 0) {
						$$('#smtp-toolbar .preloader')[0].destroy();
					}	
					
					try { res = JSON.decode(res); }
					catch(e) { res = undefined; }
					
					if (res && res.status == 'ok') {
						alert(Joomla.JText._('CONNECTION_OK','Connection ok!'));
						return;
					}
					
					alert(Joomla.JText._('CONNECTION_FAILED','Connection failed!'));
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


} catch(e){
    if (console && console.log) console.log(e);
} });
