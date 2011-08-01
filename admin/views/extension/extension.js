/**
 * The javascript file for extension view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
try {

    // how to identificate the window and data from parent?

    var id = 0;//document.location.href;
    var dialog = window.parent.Migur.moodialogs[id];
    var data = dialog.data;
    var form = $$('form')[0];

    if (data) {

        if (typeof(data.title) != 'undefined' && $('jform_title') != null) {
            $('jform_title').set('value', data.title);
        }

        if (typeof(data.showtitle) != 'undefined' && $('jform_showtitle') != null) {
            Migur.setRadio('jform_showtitle', data.showtitle);
        }

        Object.each(data.params, function(value, name) {

            //try to find in "params" fields
            var dom = $('jform_params_' + name);

            // if element is found
            if (dom) {
                if (dom.hasClass('radio')) {
                    // Handle radios
                    Migur.setRadio(dom, value);
                } else {

                    // Simply set data if type of the element is not "radio"
                    dom.set('value', value);
                }
            }
        });
    }
} catch(e){
    if (console && console.log) console.log(e);
}
});

