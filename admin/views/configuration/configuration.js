/**
 * The javascript file for configuration view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

//Migur.app.autoconfirmManager = {
//
//	init: function(){
//		$$('#jform_users_autoconfirm0, #jform_users_autoconfirm1').addEvent('change', this.updateControls);
//		this.updateControls();
//	},
//
//	updateControls: function() {
//
//		var val = $('jform_users_autoconfirm0').getProperty('checked');
//		$('jform_confirm_mail_subject').setProperty('readonly', val);
//		$('jform_confirm_mail_subject').setStyle('color', val? '#ccc' : '#666');
//		$('jform_confirm_mail_body').setProperty('readonly', val);
//		$('jform_confirm_mail_body').setStyle('color', val? '#ccc' : '#666');
//	}
//}


window.addEvent('domready', function() {

        // Add events to controls
        $$('[name=newsletter_clear_db]')[0].addEvent('click', function(){

            if ( confirm(Joomla.JText._('DO_YOU_REALY_WANT_TO_DELETE_ALL_SENT_LETTERS_QM', 'Do you realy want to delete all sent letters?')) ) {
                new Request.JSON({
                    url: '?option=com_newsletter&task=newsletter.clearsent',
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


		/**
		 * Just change a URL of a EDIT button on changing a smtp profile in select list
		 */

		var smtpSelectList = $('jform_general_smtp_default');
		var smtpEditButton = $('ctrl-smtpprofile-edit');

		var _ctrlSmtpProfileEditHandler = function(ev) {

            ev && ev.stop();

			var id = smtpSelectList.getProperty('value');

			smtpEditButton.setProperty('href', smtpEditButton.getProperty('data-href')+id);
        }

        smtpSelectList.addEvent('change', _ctrlSmtpProfileEditHandler);
		_ctrlSmtpProfileEditHandler();


        $('ctrl-smtpprofile-delete').addEvent('click', function(ev){

			if (!confirm(Joomla.JText._('ARE_YOU_SURE_QM', 'Are you sure?'))) {
				return false;
			}

            $$('[name=task]')[0].setProperty('value', 'smtpprofiles.delete');
            $$('[name=adminForm]')[0].submit();
        });


		/**
		 * Just change a URL of a EDIT button on changing a mailbox profile in select list
		 */
		var mailboxSelectList = $('jform_general_mailbox_default');
		var mailboxEditButton = $('ctrl-mailboxprofile-edit');

		var _ctrlMailboxProfileEditHandler = function(ev) {

            ev && ev.stop();

			var id = mailboxSelectList.getProperty('value');

			(id < 1)?
				mailboxEditButton.addClass('disabled') :
				mailboxEditButton.removeClass('disabled');

			mailboxEditButton.setProperty('href', mailboxEditButton.getProperty('data-href')+id);
        }

        mailboxSelectList.addEvent('change', _ctrlMailboxProfileEditHandler);
		_ctrlMailboxProfileEditHandler();


        $('ctrl-mailboxprofile-delete').addEvent('click', function(ev){

            ev && ev.stop();

			if (!confirm(Joomla.JText._('ARE_YOU_SURE_QM', 'Are you sure?'))) {
				return;
			}

            $$('[name=task]')[0].setProperty('value', 'mailboxprofiles.delete');
            $$('[name=adminForm]')[0].submit();
        });


        $('export-button').addEvent('click', function(){
            $$('[name=task]')[0].setProperty('value', 'configuration.export');
            $$('[name=adminForm]')[0].submit();
        });
});

