/**
 * The javascript file for configuration view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

Migur.app.autoconfirmManager = {

	init: function(){
		$$('#jform_users_autoconfirm0, #jform_users_autoconfirm1').addEvent('change', this.updateControls);
		this.updateControls();
	},
	
	updateControls: function() {
		
		var val = $('jform_users_autoconfirm0').getProperty('checked');
		$('jform_confirm_mail_subject').setProperty('readonly', val);
		$('jform_confirm_mail_subject').setStyle('color', val? '#ccc' : '#666');
		$('jform_confirm_mail_body').setProperty('readonly', val);
		$('jform_confirm_mail_body').setStyle('color', val? '#ccc' : '#666');	
	}
}


window.addEvent('domready', function() {
    try {

        // Add events to controls
        $$('[name=newsletter_clear_db]')[0].addEvent('click', function(){

            if ( confirm(Joomla.JText._('DO_YOU_REALY_WANT_TO_DELETE_ALL_SENT_LETTERS_QM', 'Do you realy want to delete all sent letters?')) ) {
                new Request.JSON({
                    url: '?option=com_newsletter&task=newsletter.clearsent&format=json',
                    data: {},
                    onComplete: function(res){
                        if (res && res.state) {
                            alert(Joomla.JText._('THE_DATA_DELETED_SUCCESSFULLY','The data deleted successfully'));
                        } else {
                            alert(Joomla.JText._('AN_ERROR_HAS_OCCURED_DURING_THE_REQUEST','An error has occured during the request'));
                        }
                    }
                }).send();
            }
        });

        // Add events to controls
        $$('[name=newsletter_smtp_create]')[0].addEvent('click', function(ev)
		{
            ev.stop();
            var href = "index.php?option=com_newsletter&view=smtpprofile&tmpl=component";

            SqueezeBox.open(href, {
                handler: 'iframe',
                size: {
                    x: 400,
                    y: 590
                }
            });
        });

        $$('[name=newsletter_smtp_edit]')[0].addEvent('click', function(ev)
		{
            ev.stop();
			
			var id = $('jform_general_smtp_default').getProperty('value');
			
            var href = "index.php?option=com_newsletter&task=smtpprofile.edit&tmpl=component&smtp_profile_id="+id;

            SqueezeBox.open(href, {
                handler: 'iframe',
                size: {
                    x: 400,
                    y: 590
                }
            });
        });


        // Add events to controls
        $$('[name=newsletter_mailbox_create]')[0].addEvent('click', function(ev)
		{
            ev.stop();
            var href = "index.php?option=com_newsletter&view=mailboxprofile&tmpl=component";

            SqueezeBox.open(href, {
                handler: 'iframe',
                size: {
                    x: 400,
                    y: 340
                }
            });
        });

        $$('[name=newsletter_mailbox_edit]')[0].addEvent('click', function(ev)
		{
            ev.stop();
			
			var id = $('jform_general_mailbox_default').getProperty('value');
			
			if (id < 1) {
				alert(Joomla.JText._('PLEASE_SELET_PROFILE','Please selet profile'));
				return;
			}
			
            var href = "index.php?option=com_newsletter&task=mailboxprofile.edit&tmpl=component&mailbox_profile_id="+id;

            SqueezeBox.open(href, {
                handler: 'iframe',
                size: {
                    x: 400,
                    y: 340
                }
            });
        });


        $$('[name=newsletter_smtp_delete]')[0].addEvent('click', function(ev){
			
			if (!confirm('Are you sure?')) { 
				return false;
			}
			
            $$('[name=task]')[0].setProperty('value', 'smtpprofiles.delete');
            $$('[name=adminForm]')[0].submit();
        });

        $$('[name=newsletter_mailbox_delete]')[0].addEvent('click', function(ev){
			
			if (!confirm(Joomla.JText._('ARE_YOU_SURE_QM', 'Are you sure?'))) { 
				return false;
			}
			
            $$('[name=task]')[0].setProperty('value', 'mailboxprofiles.delete');
            $$('[name=adminForm]')[0].submit();
        });


        $('export-button').addEvent('click', function(){
            $$('[name=task]')[0].setProperty('value', 'configuration.export');
            $$('[name=adminForm]')[0].submit();
        });


		// Manage accessibility to subject/body with autoconfirm flag
		Migur.app.autoconfirmManager.init();

    } catch(e){
        if (console && console.log) console.log(e);
    }
});

