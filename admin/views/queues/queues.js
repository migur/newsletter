/**
 * The javascript file for queues view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
	
	$('toolbar-queue').addEvent('click', function(ev){

		ev.stop();

		$('toolbar-preloader').addClass('preloader');

		new Request({
			url: migurSiteRoot + 'index.php?option=com_newsletter&task=cron.mailing&forced=true&sessname=' + sessname,
			onComplete: function(result){

				$('toolbar-preloader').removeClass('preloader');

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

				if (data.length > 0) {

					Array.each(data, function(el){
						count += el.processed;
					});

					if (count == 0) {
						text = Joomla.JText._('THERE_ARE_NO_EMAILS_TO_SEND','No emails to send.\nMails can not be sent to disabled or inactive subscribers.\nPlease check if some of them are in queue.');
						alert(text); 
						return;
					}

					if (count > 0) {
						text = ""+count+" "+Joomla.JText._('NEWSLETTERS_HAS_BEEN_SENT_SUCESSFULLY', 'newsletters has been sent sucessfully');
						alert(text);
						window.location.reload();
					}	


				} else {
					alert(parser.getMessagesAsList());
				}	

			}

		}).send();

	});

	// Handler for "process bounces" button
	$('toolbar-bounces').addEvent('click', function(ev){

		ev.stop();

		$('toolbar-preloader').addClass('preloader');

		/* Setup functionality for step */
		new Request({
			url: migurSiteRoot + 'index.php?option=com_newsletter&task=cron.processbounced&forced=true&sessname=' + sessname,
			onComplete: function(result){

				$('toolbar-preloader').removeClass('preloader');

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
					count++;
				});

				if (count > 0) {
					text = Joomla.JText._('BOUNCE_CHECK_COMPLETED', 'Bounce check completed') + text;
				} else {
					text = Joomla.JText._('YOU_DONT_HAVE_CONFIGURED_MAILBOXES', 'Nothing to check. \nYou don\'t have mailboxes configured or \nthere are no sent mails for them. \nIf so then go to Configuration panel to configure them.') + text;
				}	

				alert(text);
				window.location.reload();
			}	
		}).send();

	});
});
