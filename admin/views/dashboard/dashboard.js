/**
 * The javascript file for dashboard view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

migurUpdater = {

	latestVersionPath: '#updater-latestversion',
	
	scope: ['com_newsletter'],

	fillOnError: function() {
		
		$$(this.latestVersionPath).set('html', Joomla.JText._('UNKNOWN', 'Unknown'));
	},

	fillOnSuccess: function(data) {
		
		if (data['com_newsletter'].version) {
			$$(this.latestVersionPath).set('html', data['com_newsletter'].version);
		}
	},
	
	get: function() {

		var $this = this; 
	
		$$(this.latestVersionPath).set('html', '...');
	
		new Request({

			url: migurSiteRoot + 'administrator/index.php?option=com_newsletter&task=updater.getupdates',

			data: { scope: this.scope },

			onComplete: function(response){

				var parser = new Migur.jsonResponseParser();
				
				parser.setResponse(response);
				
				if (parser.isError()) {
					return $this.fillOnError();
				}

				return $this.fillOnSuccess(parser.getData());
			}

		}).send();
	}
}	


window.addEvent('domready', function() {
try {

    $('toolbar-queue').addEvent('click', function(ev){

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

    $('toolbar-bounces').addEvent('click', function(ev){

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


    var width = (mailsSent / mailsTotal) * $$('.progress-bar')[0].getWidth();

    $$('.progress-line')[0].setStyle('width', width + 'px');
	
	
	// Updater request
	migurUpdater.get();
	
} catch(e){
    if (console && console.log) console.log(e);
}

});

