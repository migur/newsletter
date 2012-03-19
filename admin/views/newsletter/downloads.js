/**
 * The javascript file for newsletter view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */


window.addEvent('domready', function() {
try {

	Migur.fileRemoveClickHandler = function(event){

		event.stop();

		var nid = $$('[name=newsletter_id]')[0].getProperty('value');

		if (nid == '' || nid < 1) {
			alert(Joomla.JText._('PLEASE_SAVE_THE_NEWSLETTER_FIRST', "Please save the newsletter first!"));
			return false;
		}

		var el = this;
		var id = el.getProperty('rel')

		new Request.JSON({
			url: migurSiteRoot + 'administrator/index.php?option=com_newsletter&task=newsletter.fileunbind&format=json',
			data: {
				download_id: id
			},
			onComplete: function(res){

				if (res.state && res.state == 1) {
					$(el).getParent('tr').destroy();
				} else {
					alert(res.error);
				}
			}
		}).send();
	}


    $$('.remove-link').each(function(el){
        el.addEvent('click', Migur.fileRemoveClickHandler);
	});	

    if ($('element-box') != null) {
        $('element-box').addEvent('mediaselected', function(event){

            if (event.id == 'fileattach') {

                new Request.JSON({
                    url: migurSiteRoot + 'administrator/index.php?option=com_newsletter&task=newsletter.fileattach&format=json',
                    data: {
                        filename: event.value,
                        newsletter_id: $$('[name="newsletter_id"]')[0].getProperty('value')
                    },
                    onComplete: function(res){

                        if (res.state && res.state == 1) {
							var cnt = $$('#attlist-container tbody tr').length;
							
							// TODO: ADD CORRECT IMAGE RELATIVE PATH
                            var tr = new Element('tr',
								{	'class': 'row'+(cnt%2),
									'html':
										'<td>'+res.data.filename+'</td><td>'+res.data.size+'</td><td>'+res.data.type+'</td>'+
										'<td class="center">'+
										'<a rel="'+res.data.downloads_id+'" class="remove-link" href="#">' +
										'<img border="0" src="' + migurSiteRoot + 'media/media/images/remove.png" alt="'+Joomla.JText._('REMOVE', 'Remove')+'" style="margin:0;">'+
										'</a>'+
										'</td>' }
							);
							
							tr.inject($$('#attlist-container tbody')[0]);		
							tr.getElements('.remove-link')[0].addEvent('click', Migur.fileRemoveClickHandler);
                        } else {
                            alert(res.error);
                        }
                    }
                }).send();
            }
        });
    }

	$('newsletter_upload').addEvent('click', function(ev){

		ev.stop();

		var nid = $$('[name=newsletter_id]')[0].getProperty('value');

		if (nid == '' || nid < 1) {
			alert(Joomla.JText._('PLEASE_SAVE_THE_NEWSLETTER_FIRST', "Please save the newsletter first!"));
			return false;
		}


		var href = migurSiteRoot + "administrator/index.php?option=com_newsletter&view=media&tmpl=component&asset=&author=&fieldid=fileattach&folder=";

		window.migurFieldId = 'fileattach'

		SqueezeBox.open(href, {
			handler: 'iframe',
			size: {
				x: 700,
				y: 720
			}
		});


		return true;
	});



} catch(e){
    if (console && console.log) console.log(e);
}

});
