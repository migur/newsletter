/**
 * The javascript file for statistic view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() { try {


    $$('[name=days]')[0].addEvent('change', function(){
        $$('[name=statisticForm]')[0].submit();
    });

} catch(e) {
    if (console && console.log) console.log(e);
} });
