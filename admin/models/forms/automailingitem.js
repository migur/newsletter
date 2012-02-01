/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {


	document.formvalidator.timeInterval = function(value) {

		var start  = $('jform_time_start').getProperty('value');
		var offset = $('jform_time_offset').getProperty('value');

		return start || offset;
	}



    document.formvalidator.setHandler('newsletter',  function (value) {
		return value > 0;
	});
	
	document.formvalidator.setHandler('time_start',  document.formvalidator.timeInterval);

	document.formvalidator.setHandler('time_offset',  document.formvalidator.timeInterval);
});