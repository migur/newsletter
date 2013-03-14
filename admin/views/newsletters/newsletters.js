/**
 * The javascript file for newsletters view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
    try {

        $('ctrl-showstats').addEvent('click', function(ev){

            var listIds = [];
            $$('#container-newsletters [name=cid[]]').each(function(el){

                if( $(el).getProperty('checked') ) {

                    listIds.push($(el).get('value'));
                }
            });

            if (listIds.length < 1) {
                alert(Joomla.JText._('PLEASE_SELECT_THE_NEWSLETTERS',"Please select the newsletters"));
				ev.stop();
                return false;
            }

            /**
             * That CSS selector will find all <a> elements with the
             * attribute rel="boxed"
             *
             * The second argument sets additional options.
             */
            var href = $(this).getProperty('href');

            var url = href + '&newsletters=' + listIds.toString();
			document.location.href = url;
        });

    } catch(e) {
        if (console && console.log) console.log(e);
    }
});
