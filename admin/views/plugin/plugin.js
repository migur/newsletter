/**
 * The javascript file for list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

migurPluginManager = {
	
	cancel: function() {
		var url = migurSiteRoot+'administrator/index.php?option=com_newsletter' + 
			'&pluginname=' + $$('[name="pluginname"]')[0].value +
			'&pluginevent=onMigurImportShowRules' +
			'&task=plugin.triggerListimport' +
			'&list_id=' + $$('[name="list_id"]')[0].value +
			'&tmpl=component' + 
			'&format=html';

		window.location.href = url;
	}

} 
