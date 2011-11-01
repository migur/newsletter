/**
 * The javascript file for dashboard view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
try {

    $('toolbar-progress').set({
        html:
            '<div class="progress-info">' +
                emailsSent + ' of ' + emailsTotal + ' emails sent in ' + newslettersSent + ' newsletters' +
            '</div>' +
            '<a href="#" class="queue-list">Process queue</a>' +
            '<a href="#" class="bounces-list">Process bounces</a>' +
			'<div class="clr"></div>' +
            '<div class="progress-line"></div>' +
            '<div class="progress-bar"></div>' +
			'<div style="float:right; min-width:0; margin:6px 4px;" id="process-preloader"></div>'
    })

    $$('#toolbar-progress .queue-list')[0].addEvent('click', function(ev){

		$('process-preloader').addClass('preloader');
		
        new Request({
            url: siteRoot + 'index.php?option=com_newsletter&task=cron.send&forced=true&sessname=' + sessname,
            onComplete: function(result){
				
				$('process-preloader').removeClass('preloader');
				
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
                    text = "There are no emails to send";
	                alert(text); 
					return;
                }
                if (res.error == '' && res.count > 0) {
                    text = ""+res.count+" newsletters has been sent sucessfully";
					alert(text);
					window.location.reload();
				}	
            }
        }).send();

    });

    $$('#toolbar-progress .bounces-list')[0].addEvent('click', function(ev){

		$('process-preloader').addClass('preloader');
		
        new Request({
            url: siteRoot + 'index.php?option=com_newsletter&task=cron.processbounced&forced=true&sessname=' + sessname,
            onComplete: function(result){

				$('process-preloader').removeClass('preloader');

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
                    text = "There are no bounced emails";
	                alert(text); 
					return;
                }
				
                if (res.error == '' && res.count > 0) {
                    text = ""+res.count+" bounced emails has been processed sucessfully";
					alert(text);
					return;
				}	
            }
        }).send();

    });

    var width = (emailsSent / emailsTotal) * $$('.progress-bar')[0].getWidth();

    $$('.progress-line')[0].setStyle('width', width + 'px');

} catch(e){
    if (console && console.log) console.log(e);
}

});

