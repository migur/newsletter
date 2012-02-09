/**
 * The javascript file for logs view.
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
		
	// Handler for "process bounces" button
    $$('#toolbar-alert a')[0].addEvent('click', function(){

		var bounceProcess = new Migur.multistepProcess;
		
		bounceProcess.begin = function(){
			
			$('toolbar-preloader')
				.addClass('preloader')
				.set('text', '')
				.set('styles', {'display': 'block', 'min-width': '60px'});
			
			this.data.processed = 0;
			this.data.totalBounces = 0;
			this.data.mailboxes = {};
			
		};

		/* Setup functionality for step */
		bounceProcess.step = function(){
			new Request({
				url: migurSiteRoot + 'index.php?option=com_newsletter&task=cron.processbounced&forced=true&limit=100&sessname=' + sessname,
				onComplete: function(res){
					bounceProcess.onComplete(res);
				}
			}).send();
		}	

		/* Setup functionality for step */
		bounceProcess.processResult = function(res){
			
			try {var data = JSON.decode(res);} 
			catch(e) {return false;}
			
			var success = true;
			var processedNow = 0;
			this.data.totalBounces	= 0;
			
			/* Add info into summary*/
			var _this = this;
			Object.each(data, function(element, key){
				
				// Handle errors
				if (key == 'errors') {
					this.data.errors = this.data.errors.split(element);
					return;
				}
				
				// Handle mailboxes
				if (_this.data.mailboxes[key] == undefined) {
					_this.data.mailboxes[key] = element
				} else {
					
					_this.data.mailboxes[key].total = element.total;
					_this.data.mailboxes[key].totalBounces = element.totalBounces;
					_this.data.mailboxes[key].errors = 
						_this.data.mailboxes[key].errors.concat(element.errors);
					
					_this.data.mailboxes[key].processed += element.processed;
				}

				/* Check for errors an if it happens then return false */
				success = (element.errors.length == 0);
				
				_this.data.totalBounces += _this.data.mailboxes[key].found;
				_this.data.processed    += element.processed;
					
				processedNow += element.processed;
			});

			// Set percent
			if (this.data.totalBounces != 0) {
				$('toolbar-preloader').set(
					'text', 
					Math.round(parseFloat(this.data.processed) / parseFloat(this.data.totalBounces) * 100) + '%');
			}		
			
			return success && processedNow > 0;
		}	

		/* Setup functionality for end */
		bounceProcess.end = function(){

			var res = this.data.mailboxes;

			$('toolbar-preloader')
				.removeClass('preloader')
				.set('styles', {'display': 'none'})
				.set('text', '');

			var text = Joomla.JText._("BOUNCES_PROCESSING_COMPLETED","Bounces processing completed")+"\n\n";

			if (typeof res == 'undefined') {
				alert(Joomla.JText._("AN_UNKNOWN_ERROR_OCCURED","An unknown error occured"));
				window.location.reload();
				return;
			}

			if (typeof res.error != 'undefined') {
				alert(res.error);
				return;
			}

			var len = 0;
			Object.each(res, function(elem, key){
				text += "\n "+key+' '+Joomla.JText._("MAILBOX","mailbox")+':';
				text += "\n "+elem.found + ' ' + Joomla.JText._("BOUNCES_FOUND","bounced mails found");
				text += "\n "+elem.processed + ' ' + Joomla.JText._("BOUNCES_PROCESSED","bounced mails processed");
				text += "\n "+elem.errors.join(' ');
				len++;
			});

			if (len>0) {

				alert(text);
				window.location.reload();

			} else {

				alert(Joomla.JText._('THERE_ARE_NO_MAILBOXES_TO_PROCESS',"There are no mailboxes to process"));
				return;
			}
		}
		
		bounceProcess.start();
    });


    } catch(e) {
        if (console && console.log) console.log(e);
    }
});
