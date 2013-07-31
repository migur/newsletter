/**
 * The javascript file for list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */


window.addEvent('domready', function() { try {

    $$('[role="pluginButton"]').addEvent('click', function(ev){

		ev.stop();

		// Hide standard import form
		if ($('import-file')) {
			$('import-file').addClass('hide');
		}

		// Getting list id
		var listId = 0;
		if ($$('[name="list_id"]').length > 0) {
			listId = $$('[name="list_id"]')[0].getProperty('value');
		}

		var pane = $$('.plugin-pane')[0];

//		$$('.plugin-preloader')[0].addClass('preloader');

		var url = 
			migurSiteRoot+'administrator/index.php?option=com_newsletter' + 
			'&pluginname=' + $(this).getProperty('rel') +
			'&pluginevent=onMigurImportShowRules' +
			'&task=plugin.triggerListimport' +
			'&list_id=' + listId +
			'&tmpl=component' + 
			'&format=html';

		pane.setProperty('src', url);

//		$$('.plugin-preloader')[0].removeClass('preloader');

		pane.removeClass('hide');
		pane.removeClass('preloader');
		pane.removeEvents('click');
    });


} catch(e){ if (console && console.log) console.log(e); }
});
