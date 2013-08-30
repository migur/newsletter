/**
 * The javascript file for templates view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {

	$$('#templates-edit .toolbar').each(function(el) { el.removeProperty('onclick'); });

	$$('.templateslist [type=checkbox]').addEvent('click', function(){

       var active = $$('.templateslist [type=checkbox]').some(function(el){
           return el.get('checked');
       });

       if( active ) {
            $$('#templates-edit, #templates-trash').each(function(el){
	            el.getElements('span')[0].removeClass('toolbar-inactive');
			});

        } else {
            $$('#templates-edit, #templates-trash').each(function(el){
                el.getElements('span')[0].addClass('toolbar-inactive');
			});
       }
   });


/* Expand the functionality of the edit button */
if ($$('#templates-edit a').length > 0) {
    $$('#templates-edit a')[0].addEvent('click', function(ev){

        ev.stop();

        if ($$('[name=cid[]]').length > 0) {
            $$('[name=cid[]]').each(function(el){
                if(el.getProperty('checked')) {
                    el.getParent('tr').getElements('.modal').fireEvent('click');
                }
            });
        }
    });
}

$$('.templateslist [type=checkbox]')[0].fireEvent('click');

if ($$('.templateslist .search').length > 0) {

	tplTransport = null;
	$$('.templateslist .search').addEvent('click', function(){

		var id = $(this).getParent('tr').getElements('[name=cid[]]')[0].get('value');

		delete tplTransport;
		tplTransport = new Request.JSON({
			url: '?option=com_newsletter&task=template.getparsed&shownames=1&format=html',
			data: {
				t_style_id: id,
				tagsRenderMode: 'schematic',
				type: 'html' },

			onComplete: function(res){

				$('preview-container').set('html', res.data.content);
				$('tpl-title').set('text', res.data.information.name);
				$('tpl-name').set('text', res.data.information.author);
				$('tpl-email').set('text', res.data.information.authorEmail);
			}
		}).send();

		return false;
	});


	$$('.templateslist .search')[0].fireEvent('click');
}

});
