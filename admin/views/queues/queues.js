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
				url: migurSiteRoot + 'index.php?option=com_newsletter&task=cron.mailing&forced=true&sessname=' + sessname,
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
    $$('#toolbar-alert a')[0].addEvent('click', function(){

		var bounceProcess = new Migur.multistepProcess;
		
		bounceProcess.begin = function(){
			
			$('toolbar-preloader')
				.addClass('preloader')
				.set('text', '0%')
				.set('styles', {'display': 'block', 'width': '60px'});
			
			this.data.fetched = 0;
			this.data.total = 0;
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
			var fetched = 0;
			var total = 0;
			
			/* Add info into summary*/
			var _this = this;
			Object.each(data, function(element, key){
				
				if (_this.data.mailboxes[key] == undefined) {
					_this.data.mailboxes[key] = element
				} else {
					
					_this.data.mailboxes[key].fetched += element.fetched;
					_this.data.mailboxes[key].processed += element.processed;
					_this.data.mailboxes[key].errors.concat(element.errors);
				}

				/* Check for errors an if it happens then return false */
				success = (element.errors.length == 0);
				fetched += element.fetched;
				total += element.total;
			});

			// Add the count of fetched
			this.data.fetched += fetched;

			// Set total count
			this.data.total = total;

			// Set percent
			if (this.data.total != 0) {
				$('toolbar-preloader').set(
					'text', 
					Math.round(parseFloat(this.data.fetched) / parseFloat(this.data.total) * 100) + '%');
			}		
			
			return success && fetched > 0;
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
				text += "\n "+elem.processed + ' ' + Joomla.JText._("BOUNCES_FOUND","bounces found");
				text += "\n "+elem.fetched + ' ' + Joomla.JText._("NEW_MAILS","new mails");
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
