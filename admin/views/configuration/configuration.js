/**
 * The javascript file for configuration view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
    try {

        // Add events to controls
        $$('[name=newsletter_clear_db]')[0].addEvent('click', function(){

            if ( confirm("Do you realy want to delete all sent letters?") ) {
                new Request.JSON({
                    url: '?option=com_newsletter&task=newsletter.clearsent&format=json',
                    data: {},
                    onComplete: function(res){
                        if (res && res.state) {
                            alert('The data deleted successfully');
                        } else {
                            alert('An error has occured during the request');
                        }
                    }
                }).send();
            }
        });

        // Add events to controls
        $$('[name=newsletter_smtp_create]')[0].addEvent('click', function(ev){

            ev.stop();

            var href = "index.php?option=com_newsletter&view=smtpprofile&tmpl=component";

            SqueezeBox.open(href, {
                handler: 'iframe',
                size: {
                    x: 400,
                    y: 400
                }
            });
        });


        $$('[name=newsletter_smtp_delete]')[0].addEvent('click', function(ev){
            $$('[name=task]')[0].setProperty('value', 'smtpprofiles.delete');
            $$('[name=adminForm]')[0].submit();
        });


        $('export-button').addEvent('click', function(){
            $$('[name=task]')[0].setProperty('value', 'configuration.export');
            $$('[name=adminForm]')[0].submit();
        });

    } catch(e){
        if (console && console.log) console.log(e);
    }
});

