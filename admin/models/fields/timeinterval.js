/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
	
	$$('.timeinterval .timeinterval-count').addEvent('keyup', function(ev){
		
		var valueCnt = $(this).getProperty('value');
		var valueType = $(this).getSiblings('.timeinterval-type')[0].getProperty('value');
		var seconds = 0;
		
		if(valueType == 'day') {
			seconds = valueCnt * 24 * 3600;
		}
		
		if(valueType == 'week') {
			seconds = valueCnt * 24 * 7 * 3600;
		}

		$(this).getSiblings('.timeinterval-hidden')[0].setProperty('value', seconds);
	});

	$$('.timeinterval .timeinterval-type').addEvent('change', function(ev){
		
		var valueType = $(this).getProperty('value');
		var valueCnt = $(this).getSiblings('.timeinterval-count')[0].getProperty('value');
		var seconds;
		
		if(valueType == 'day') {
			seconds = valueCnt * 24 * 3600;
		}
		
		if(valueType == 'week') {
			seconds = valueCnt * 24 * 7 * 3600;
		}

		$(this).getSiblings('.timeinterval-hidden')[0].setProperty('value', seconds);
	});
});