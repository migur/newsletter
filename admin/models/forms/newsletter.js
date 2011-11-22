/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {

    /*
     * The validators for form fields
     */

    document.formvalidator.setHandler('name',
        function (value) {
            regex=/^.{1,255}$/;
            return regex.test(value);
        });

    document.formvalidator.setHandler('subject',
        function (value) {
            regex=/^.{1,255}$/;
            return regex.test(value);
        });

    document.formvalidator.setHandler('t_style_id',
        function (value) {
            return value != "";
        });

    document.formvalidator.setHandler('newsletter_from_name',
        function (value) {
            regex=/^.{1,255}$/;
            return regex.test(value);
        });

    document.formvalidator.setHandler('newsletter_from_email',
        function (value) {
            regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
            return regex.test(value);
        });

    document.formvalidator.setHandler('newsletter_to_name',
        function (value) {
            regex=/^.{1,255}$/;
            return regex.test(value);
        });

    document.formvalidator.setHandler('newsletter_to_email',
        function (value) {
            regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
            return regex.test(value);
        });

    document.formvalidator.setHandler('plain',
        function (value) {
            regex=/^[.]{1,5000}$/;
            return regex.test(value);
        });

    document.formvalidator.setHandler('alias',
        function (value) {
            return true;
        });
});