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
			'<div style="float:left">' +
				'<div class="progress-info">' +
	                emailsSent + ' of ' + emailsTotal + ' emails sent in ' + newslettersSent + ' newsletters' +
		        '</div>' +
				'<div style="float:right; min-width:0;" id="process-preloader"></div>' +
			'</div>' +	
			'<div style="float:right">' +
				'<a href="#" class="queue-list">'+Joomla.JText._('PROCESS_QUEUE','Process queue')+'</a><br/>' +
				'<a href="index.php?option=com_newsletter&view=queues" class="viewqueue-list">'+Joomla.JText._('VIEW_QUEUE', 'View queue')+'</a><br/>' +
				'<a href="#" class="bounces-list">'+Joomla.JText._('PROCESS_BOUNCES','Process bounces')+'</a>' +
			'</div>' +
			'<div style="width: 360px">' +
				'<div class="progress-line"></div>' +
				'<div class="progress-bar"></div>' +
			'</div>'	
    })

    $$('#toolbar-progress .queue-list')[0].addEvent('click', function(ev){

		ev.stop();

		$('process-preloader').addClass('preloader');
		
        new Request({
            url: migurSiteRoot + 'index.php?option=com_newsletter&task=cron.mailing&forced=true&sessname=' + sessname,
            onComplete: function(result){
				
				$('process-preloader').removeClass('preloader');
				
				var text;
				
				var parser = new Migur.jsonResponseParser();
				parser.setResponse(result);
				
				if (parser.isError()) {
					if (parser.getState() == 'unknown error') {
						text = Joomla.JText._('AN_UNKNOWN_ERROR_OCCURED', 'An unknown error occured');
					} else {	
						text = Joomla.JText._('AN_ERROR_OCCURED', 'An error occured!') + "\n" +
							   parser.getMessagesAsList();
					}

					alert(text);
					return;
				}

				var count = 0;
				var data = parser.getData();
				Array.each(data, function(el){
					count += el.processed;
				});

                if (count == 0) {
                    text = Joomla.JText._('THERE_ARE_NO_EMAILS_TO_SEND','There are no emails to send');
	                alert(text); 
					return;
                }
				
                if (count > 0) {
                    text = ""+count+" "+Joomla.JText._('NEWSLETTERS_HAS_BEEN_SENT_SUCESSFULLY', 'newsletters has been sent sucessfully');
					alert(text);
					window.location.reload();
				}	
            }
        }).send();

    });

    $$('#toolbar-progress .bounces-list')[0].addEvent('click', function(ev){

		ev.stop();

		$('process-preloader')
			.addClass('preloader')
			.set('styles', {'display': 'block'});

		/* Setup functionality for step */
		new Request({
			url: migurSiteRoot + 'index.php?option=com_newsletter&task=cron.processbounced&forced=true&sessname=' + sessname,
			onComplete: function(result){
				
				$('process-preloader').removeClass('preloader');
				
				var text = '';
				
				var parser = new Migur.jsonResponseParser();
				parser.setResponse(result);
				
				if (parser.isError()) {
					if (parser.getState() == 'unknown error') {
						text = Joomla.JText._('AN_UNKNOWN_ERROR_OCCURED', 'An unknown error occured');
					} else {	
						text = parser.getMessagesAsList();
					}

					alert(text);
					return;
				}
				
				var count = 0;
				var data = parser.getData();
				Object.each(data, function(el, key){
					var submsg = (el.errors.length > 0)? 
						el.errors[0] : 
						(' '+Joomla.JText._('FOUND', 'found')+' '+el.found+' '+Joomla.JText._('BOUNCED_EMAILS', 'bounced emails'));
					
					text += '\n' + key + ': ' + submsg;
				});

				text = Joomla.JText._('BOUNCE_CHECK_COMPLETED', 'Bounce check completed')+text;
				alert(text);
				window.location.reload();
			}	
		}).send();

    });


    var width = (emailsSent / emailsTotal) * $$('.progress-bar')[0].getWidth();

    $$('.progress-line')[0].setStyle('width', width + 'px');

} catch(e){
    if (console && console.log) console.log(e);
}

});

