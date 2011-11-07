/**
 * The javascript file for import view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
try {

    historyPaginator = new Migur.lists.paginator($$('.sslist')[0]);
    Migur.lists.sortable.setup($$('.sslist')[0]);

    $$('#sender-export a')[0].addEvent('click', function(event){

        event.stop();

        var newsletterId = $$('select')[0].get('value');

        if ( !newsletterId ) {
            alert(Joomla.JText._('PLEASE_SELECT_NEWSLETTER_FIRST','Please select newsletter first'));
            return;
        }

        var lists = [];
        $$('[name=cid[]]').each(function(el){
            if (el.getProperty('checked')) {
            lists.push(el.get('value'));
        }
        });

        if ( lists.length == 0 ) {
            alert(Joomla.JText._('PLEASE_SELECT_AT_LEAST_ONE_LIST','Please selct at least one list'));
            return;
        }


        if ( confirm(Joomla.JText._('DO_YOU_REALY_WANT_TO_SEND_THIS_NEWSLETTER_QM', 'Do you realy want to send this newsletter?'))) {

            new Request.JSON({
            url: '?option=com_newsletter&task=sender.addtoqueue&format=json',
            data: {
                lists: lists,
                newsletter_id: newsletterId
            },
                onComplete: function(res){
                    if (res && res.state) {
                        alert(Joomla.JText._('THE_NEWSLETTER_HAS_BEEN_QUEUED_SUCCESFULLY', 'The newsletter has been queued succesfully'));
                        window.parent.SqueezeBox.close();
                        window.parent.location.reload();
                    } else {
                        alert(Joomla.JText._('AN_ERROR_HAS_OCCURED_DURING_THE_REQUEST','An error has occured during the request'));
                    }
                }
            }).send();
        }
    });


    
} catch(e){
    if (console && console.log) console.log(e);
}
});
