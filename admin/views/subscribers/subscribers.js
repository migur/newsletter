/**
 * The javascript file for subscribers view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() { try {


    $$('#subscribers-copy a, #subscribers-cancel a').addEvent('click', function(ev){
        
        ev.stop();

        var task = $(this).getProperty('href');
        var type = (task.indexOf('assign') > 0)? 'assign' : 'remove';

        var listIds = [];
        $$('#form-lists [name=cid[]]').each(function(el){

            if( $(el).getProperty('checked') ) {

                listIds.push($(el).get('value'));
            }
        });

        if ( $$('#form-subscribers [name=boxchecked]')[0].get('value') < 1 ) {
            if (type == 'assign') {
                alert(Joomla.JText._(
					'PLEASE_SELECT_THE_SUBSCRIBERS_TO_ASSIGN_TO_LIS',
					"Please select the subscribers to assign to list"
				));
            } else {
                alert(Joomla.JText._(
					'PLEASE_SELECT_THE_SUBSCRIBERS_TO_REMOVE_FROM_LISTS',
					"Please select the subscribers to remove from lists"
				));
            }
            return;
        }

        if (listIds.length == 0) {
            if (type == 'assign') {
                alert(Joomla.JText._(
					'IN_THE_TABLE_WITH_LISTS_ON_THE_RIGHT_SELECT',
					'In the table with "Lists" on the right, select at least one list to assign the selected subscriber(s) to.'
				));
            } else {
                alert(Joomla.JText._(
					'IN_THE_TABLE_WITH_LISTS_ON_THE_RIGHT_SELECT_FROM',
					'In the table with "Lists" on the right, select at least one list to remove the selected subscriber(s) from.'
				));
            }
            return;
        }
/*
        if (listIds.length > 1) {
            alert("Please select only one list");
            return;
        }
*/
        $$('#form-subscribers [name=list_id]')[0].set('value', JSON.encode(listIds));

        var task = $(this).getProperty('href');
        Joomla.submitform(task, document.subscribersForm);
    });






    $('form-subscribers').getElements('[name=cid[]], [name=checkall-toggle]').addEvent('click', function(){

        if ($$('#form-subscribers [name=boxchecked]')[0].get('value') == 0) {
            $$('#subscribers-copy span')[0].addClass('disabled');
            $$('#subscribers-cancel span')[0].addClass('disabled');
        } else {
            $$('#subscribers-copy span')[0].removeClass('disabled');
            $$('#subscribers-cancel span')[0].removeClass('disabled');
        }
    });

    $$('#subscribers-copy span')[0].addClass('disabled');
    $$('#subscribers-cancel span')[0].addClass('disabled');


//	if ($('conflict-resolver-link')) {
//			
//		$('conflict-resolver-link').addEvent('click', function(ev){
//			ev.stop();
//			
//			var url = migurSiteRoot + 'administrator/index.php?option=com_newsletter&view=conflicts&tmpl=component';
//			
//			SqueezeBox.open(url, {
//				handler: 'iframe',
//				size: {x: 900, y: 600}
//			});
//		});
//	}	

} catch(e) {
    if (console && console.log) console.log(e);
} });
