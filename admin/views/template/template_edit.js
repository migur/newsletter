/**
 * The javascript file for templates view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
try {

	var nativeInsertFieldValue = jInsertFieldValue;

	jInsertFieldValue = function (value, id) {

		nativeInsertFieldValue(value, id);

		new Request.JSON({
			url: '?option=com_newsletter&task=file.fileinfo',
			data: {
				filename: migurSiteRoot + value
			},

			onComplete: function(res){
				
				$(id).setProperty('value', migurSiteRoot + value);

				if (res.data.mime.substr(0,5) == 'image') {

					if (typeof res.data['0'] != 'undefined') {
						res.data['0'] += 'px';
					} else {
						res.data['0'] = 'auto';
					}

					if (typeof res.data['1'] != 'undefined') {
						res.data['1'] += 'px';
					} else {
						res.data['1'] = 'auto';
					}

					$(id+'_width').setProperty('value', res.data['0']);
					$(id+'_height').setProperty('value', res.data['1']);
				}
			}
		}).send();
	}

} catch(e){
    if (console && console.log) console.log(e);
}
});

