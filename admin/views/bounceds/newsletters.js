/**
 * The javascript file for newsletters view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
    try {

        $$('#newsletters-default a')[0].addEvent('click', function(ev){

            ev.stop();

            var listIds = [];
            $$('#form-newsletterslist [name=cid[]]').each(function(el){

                if( $(el).getProperty('checked') ) {

                    listIds.push($(el).get('value'));
                }
            });

            if (listIds.length < 1) {
                alert(Joomla.JText._('PLEASE_SELECT_THE_NEWSLETTERS',"Please select the newsletters"));
                return;
            }

            /**
             * That CSS selector will find all <a> elements with the
             * attribute rel="boxed"
             *
             * The second argument sets additional options.
             */
            var href = $$('#newsletters-default a').getProperty('href');

            var url = href + '&newsletters=' + listIds.toString();
            SqueezeBox.open(url, {
                    handler: 'iframe',
                    size: {x: 900, y: 600}
            });
        });

    } catch(e) {
        if (console && console.log) console.log(e);
    }
});
