/**
 * The javascript file for queues view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
    try {

		$$('#toolbar-messaging a')[0].addEvent('click', function(ev){

			ev.stop();

			$('toolbar-preloader').addClass('preloader');

			new Request({
				url: migurSiteRoot + 'index.php?option=com_newsletter&task=cron.send&forced=true&sessname=' + sessname,
				onComplete: function(result){

					$('toolbar-preloader').removeClass('preloader');

					try { var res = JSON.decode(result); } 
					catch(e) {res = undefined;}

					var text;

					if (typeof res == 'undefined' || typeof res.error == 'undefined' || res.error != '') {

						var error = (typeof res == 'undefined' || typeof res.error == 'undefined')? 'unknown' : res.error;
						text = "An error occured: \n" + error;
						alert(text); 
						return;
					}

					if (res.error == '' && res.count == 0) {
						text = Joomla.JText._('THERE_ARE_NO_EMAILS_TO_SEND',"There are no emails to send");
						alert(text); 
						return;
					}
					if (res.error == '' && res.count > 0) {
						text = ""+res.count+" "+Joomla.JText._('NEWSLETTERS_HAS_BEEN_SENT_SUCESSFULLY','newsletters has been sent sucessfully');
						alert(text);
						window.location.reload();
					}	
				}
			}).send();

		});
		
	// Handler for "process bounces" button
    $$('#toolbar-alert a')[0].addEvent('click', function(ev){

		$('toolbar-preloader').addClass('preloader');		
		
        new Request({
            url: migurSiteRoot + 'index.php?option=com_newsletter&task=cron.processbounced&forced=true&sessname=' + sessname,
            onComplete: function(result){

				$('toolbar-preloader').removeClass('preloader');

				try { var res = JSON.decode(result); } 
				catch(e) {res = undefined;}
				
				var text;
				
                if (typeof res == 'undefined' || typeof res.error == 'undefined' || res.error != '') {
					
					var error = (typeof res == 'undefined' || typeof res.error == 'undefined')? 'unknown' : res.error;
                    text = "An error occured: \n" + error;
	                alert(text); 
					return;
                }
				
                if (res.error == '' && res.count == 0) {
                    text = Joomla.JText._('THERE_ARE_NO_BOUNCED_EMAILS',"There are no bounced emails");
	                alert(text); 
					return;
                }
				
                if (res.error == '' && res.count > 0) {
                    text = ""+res.count+" "+Joomla.JText._('BOUNCED_EMAILS_HAS_BEEN_PROCESSED_SUCESSFULLY','bounced emails has been processed sucessfully');
					alert(text);
					window.location.reload();
					return;
				}	
            }
        }).send();

    });


    } catch(e) {
        if (console && console.log) console.log(e);
    }
});
