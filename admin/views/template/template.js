/**
 * The javascript file for templates view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

function jInsertFieldValue(value, id) {

    new Request.JSON({
        url: '?option=com_newsletter&task=file.fileinfo&format=json',
        data: {
            filename: migurSiteRoot + value
        },

        onComplete: function(res){
            
            $(id).setProperty('value', migurSiteRoot + value);

            if (res.mime.substr(0,5) == 'image') {
				
				if (typeof res['0'] != 'undefined') {
					res['0'] += 'px';
				} else {
					res['0'] = 'auto';
				}

				if (typeof res['1'] != 'undefined') {
					res['1'] += 'px';
				} else {
					res['1'] = 'auto';
				}

                $(id+'_width').setProperty('value', res['0']);
                $(id+'_height').setProperty('value', res['1']);
            }
        }
    }).send();


}

window.addEvent('domready', function() {
try {

    $$('#multitab-toolbar-cancel a')
        .removeProperty('onclick')
        .addEvent('click', function(){
            if (window && window.parent && window.parent.SqueezeBox) {
                window.parent.SqueezeBox.close();
            }
            return false;
        });
		
		
	if(typeof $$('.templateslist .search')[0] != 'undefined') {	
		
		tplTransport = null;
		$$('.templateslist .search').addEvent('click', function(){

			var id = $(this).getParent('tr').getElements('[name=cid[]]')[0].get('value');

			delete tplTransport;
			tplTransport = new Request.JSON({
				url: '?option=com_newsletter&task=template.getparsed&format=json&shownames=1',
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

} catch(e){
    if (console && console.log) console.log(e);
}
});

