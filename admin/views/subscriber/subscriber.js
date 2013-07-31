/**
 * The javascript file for subscriber view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() { try {



    $$('#subscriber-toolbar-cancel a')
        .removeProperty('onclick')
        .addEvent('click', function(){
            if (window && window.parent && window.parent.SqueezeBox) {
                window.parent.SqueezeBox.close();
            }
            return false;
        });

    if ( $$('.sshistory')[0] ) {
        historyPaginator = new Migur.lists.paginator($$('.sshistory')[0]);
        Migur.lists.sortable.setup($$('.sshistory')[0]);
    }

} catch(e){
    if (console && console.log) console.log(e);
} });
