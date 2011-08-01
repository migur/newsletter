/**
 * The javascript file for templates view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

function jInsertFieldValue(value, id) {

    new Request.JSON({
        url: '?option=com_newsletter&task=file.fileinfo&format=json',
        data: {
            filename: siteRoot + value
        },

        onComplete: function(res){
            
            $(id).setProperty('value', siteRoot + value);

            if (res.mime.substr(0,5) == 'image') {
                $(id+'_width').setProperty('value', res['0'] + 'px');
                $(id+'_height').setProperty('value', res['1'] + 'px');
            }
        }
    }).send();


}

window.addEvent('domready', function() {
try {

    $$('#multitab-toolbar-cancel a')
        .removeProperty('onclick')
        .addEvent('click', function(){
            if (window && window.parent && window.parent.SqueezeBox) {
                window.parent.SqueezeBox.close();
            }
            return false;
        });

} catch(e){
    if (console && console.log) console.log(e);
}
});

