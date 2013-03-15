/**
 * The javascript file for templates view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {

	var previewControls = $$('.templateslist .icon');

	var update = function(){

		var id = $(this).getParent('tr').getElements('[name=cid[]]')[0].get('value');
		
		new Request.JSON({
			url: '?option=com_newsletter&task=template.getparsed&shownames=1&format=html',
			data: {
				t_style_id: id,
				tagsRenderMode: 'schematic',
				type: 'html' 
			},

			onComplete: function(res){
				$('container-preloader').removeClass('preloader');
				render(res.data);
			}
			
		}).send();

		$('container-preloader').addClass('preloader');
		render({});

		return false;
	}

	var render = function(data){
		$('preview-container').set('html', data.content || '');
		$('tpl-title').set('text', (data.information && data.information.name) || '');
		$('tpl-name').set('text',  (data.information && data.information.author) || '');
		$('tpl-email').set('text', (data.information && data.information.authorEmail) || '');
	}

	// Init
	if(previewControls.length > 0) {	

		previewControls.addEvent('click', update);

		update.apply(previewControls[0]);
	}	
	
});

