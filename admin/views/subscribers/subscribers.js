/**
 * The javascript file for subscribers view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() { try {

    $('subscribers-unbind').addEvent('click', function(ev){
        
        ev.stop();

        if ( $$('#form-subscribers [name=boxchecked]')[0].get('value') < 1 ) {
			alert(Joomla.JText._(
				'PLEASE_SELECT_THE_SUBSCRIBERS_TO_REMOVE_FROM_LISTS',
				"Please select the subscribers to remove from lists"
			));
            return;
        }

        var listIds = [];
        $$('#form-lists [name=cid[]]').each(function(el){
            if( $(el).getProperty('checked') ) {
                listIds.push($(el).get('value'));
            }
        });
		
        if (listIds.length == 0) {
			alert(Joomla.JText._(
				'IN_THE_TABLE_WITH_LISTS_ON_THE_RIGHT_SELECT_FROM',
				'In the table with "Lists" on the right, select at least one list to remove the selected subscriber(s) from.'
			));
            return;
        }

		$$('#form-subscribers [name=list_id]')[0].set('value', JSON.encode(listIds));

        Joomla.submitform($(this).getProperty('data-task'), document.subscribersForm);
    });


	$('subscribers-assign').addEvent('click', function(ev){
        
        ev.stop();

        if ( $$('#form-subscribers [name=boxchecked]')[0].get('value') < 1 ) {
                alert(Joomla.JText._(
					'PLEASE_SELECT_THE_SUBSCRIBERS_TO_ASSIGN_TO_LIS',
					"Please select the subscribers to assign to list"
				));
            return;
        }
		
        var listIds = [];
        $$('#form-lists [name=cid[]]').each(function(el){
            if( $(el).getProperty('checked') ) {
                listIds.push($(el).get('value'));
            }
        });

        if (listIds.length == 0) {
                alert(Joomla.JText._(
					'IN_THE_TABLE_WITH_LISTS_ON_THE_RIGHT_SELECT',
					'In the table with "Lists" on the right, select at least one list to assign the selected subscriber(s) to.'
				));
            return;
        }
		
        $$('#form-subscribers [name=list_id]')[0].set('value', JSON.encode(listIds));

        Joomla.submitform($(this).getProperty('data-task'), document.subscribersForm);
	});	


    $('form-subscribers').getElements('[name=cid[]], [name=checkall-toggle]').addEvent('click', function(){

		console.log($$('#form-subscribers [name=boxchecked]')[0].get('value'));
        if ($$('#form-subscribers [name=boxchecked]')[0].get('value') == 0) {
            $('subscribers-assign').addClass('disabled');
            $('subscribers-unbind').addClass('disabled');
        } else {
            $('subscribers-assign').removeClass('disabled');
            $('subscribers-unbind').removeClass('disabled');
        }
    });

    $('subscribers-assign').addClass('disabled');
    $('subscribers-unbind').addClass('disabled');

} catch(e) {
    if (console && console.log) console.log(e);
} });
