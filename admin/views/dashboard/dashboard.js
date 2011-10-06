/**
 * The javascript file for dashboard view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
try {

    $('toolbar-progress').set({
        html:
            '<div class="progress-info">' +
                emailsSent + ' of ' + emailsTotal + ' emails sent in ' + newslettersSent + ' newsletters' +
            '</div>' +
            '<a href="#" class="queue-list">Process queue</a>' +
            '<div class="progress-line"></div>' +
            '<div class="progress-bar"></div>'
    })

    $$('#toolbar-progress .queue-list')[0].addEvent('click', function(ev){

        new Request.JSON({
            url: siteRoot + 'index.php?option=com_newsletter&task=cron.send&forced=true&sessname=' + sessname,
            onComplete: function(res){
                if (res.error == '' && res.count == 0) {
                    text = "There are no emails to send";
                }
                if (res.error == '' && res.count > 0) {
                    text = ""+res.count+" newsletters has been sent sucessfully";
                }
                if (res.error != '') {
                    text = "An error occured: \n"+res.error;
                }
                alert(text);
                window.location.reload();
            }
        }).send();

    });

    var width = (emailsSent / emailsTotal) * $$('.progress-bar')[0].getWidth();

    $$('.progress-line')[0].setStyle('width', width + 'px');

} catch(e){
    if (console && console.log) console.log(e);
}

});

