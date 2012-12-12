/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {

//    /*
//     * The validators for form fields
//     */
//    document.formvalidator.setHandler('name',
//        function (value) {
//            regex=/^.{1,255}$/;
//            return regex.test(value);
//        });
//
//    document.formvalidator.setHandler('from_name',
//        function (value) {
//            regex=/^.{1,255}$/;
//            return regex.test(value);
//        });
//
//    document.formvalidator.setHandler('from_email',
//        function (value) {
//            regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
//            return regex.test(value);
//        });
//
//    document.formvalidator.setHandler('reply_to_name',
//        function (value) {
//            regex=/^.{1,255}$/;
//            return regex.test(value);
//        });
//
//    document.formvalidator.setHandler('reply_to_email',
//        function (value) {
//            regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
//            return regex.test(value);
//        });
		
		
	$$('[name="jform[is_ssl]"]').addEvent('change', function(){
		
		var checkbox = $$('[name="jform[validate_cert]"]')[0];
		
		var select = $(this);
		
		if (select.get('value') == 0 && !checkbox.getProperty('disabled')) {
			Migur.validateCertValue = checkbox.getProperty('checked');
			checkbox.setProperty('disabled', 'disabled');
			checkbox.removeProperty('checked');
		}
		

		if (select.get('value') > 0 && checkbox.getProperty('disabled')) {
			checkbox.removeProperty('disabled');
			checkbox.setProperty('checked', Migur.validateCertValue);
		}
	});	
	
	$$('[name="jform[is_ssl]"]').fireEvent('change');
});