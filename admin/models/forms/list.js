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
            regex=/^.+$/;
            return regex.test(value);
        });

    document.formvalidator.setHandler('description',
        function (value) {
            regex=/^.+$/;
            return regex.test(value);
        });
});