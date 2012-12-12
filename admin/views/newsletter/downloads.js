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
			url: migurSiteRoot + 'administrator/index.php?option=com_newsletter&task=newsletter.fileunbind&format=html',
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

    if ($('tabs') != null) {
        $('tabs').addEvent('mediaselected', function(event){

            if (event.id == 'fileattach') {

                new Request.JSON({
                    url: migurSiteRoot + 'administrator/index.php?option=com_newsletter&task=newsletter.fileattach&format=html',
                    data: {
                        filename: event.value,
                        newsletter_id: $$('[name="newsletter_id"]')[0].getProperty('value')
                    },
                    onComplete: function(res){

						var parser = new Migur.jsonResponseParser(res);
						
						var data = parser.getData();

						if (parser.isError()) {
							alert( parser.getMessagesAsList(Joomla.JText._('AN_UNKNOWN_ERROR_OCCURED', 'An unknown error occured!')) );
							return;	
						}

						 if(!data) {
							alert(Joomla.JText._('NO_FILES_FOUND', 'No files found!'));
							return;
						}

						var cnt = $$('#attlist-container tbody tr').length;

						// TODO: ADD CORRECT IMAGE RELATIVE PATH
						var tr = new Element('tr',
							{	'class': 'row'+(cnt%2),
								'html':
									'<td>'+data.filename+'</td><td>'+data.size+'</td><td>'+data.type+'</td>'+
									'<td class="center">'+
									'<a rel="'+data.downloads_id+'" class="remove-link" href="#">' +
									'<img border="0" src="' + migurSiteRoot + 'media/media/images/remove.png" alt="'+Joomla.JText._('REMOVE', 'Remove')+'" style="margin:0;">'+
									'</a>'+
									'</td>' }
						);

						tr.inject($$('#attlist-container tbody')[0]);		
						tr.getElements('.remove-link')[0].addEvent('click', Migur.fileRemoveClickHandler);
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
				x: 800,
				y: 700
			}
		});


		return true;
	});



} catch(e){
    if (console && console.log) console.log(e);
}

});
