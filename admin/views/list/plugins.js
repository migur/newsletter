/**
 * The javascript file for list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */



window.addEvent('domready', function() { try {

    PluginImportManager = {
        
        init: function(options) {
            
            // Hide standard import form
            if ($('import-file')) {
                $('import-file').addClass('hide');
            }

            // Getting list id
            var listId = 0;
            if ($$('[name="list_id"]').length > 0) {
                listId = $$('[name="list_id"]')[0].getProperty('value');
            }


            new Request.HTML({
                method: 'get',
                url: migurSiteRoot+'administrator/index.php?option=com_newsletter&tmpl=component',
                data: {
                    'pluginname':  options.plugin,
                    'pluginevent': 'onMigurImportShowRules',
                    'task':        'list.importPluginTrigger',
                    'list_id':     listId
                },
                
                onComplete: function(res, res2, html) {
                    
                    var pane = $$('.plugin-pane')[0];
                    
                    pane.set('html', html);
                    pane.removeEvents('click');
                    pane.addEvent('click', PluginImportManager.onClickPluginForm);
                }
            }).send();
        },
        
        onClickPluginForm: function(ev) {

            var role = $(ev.target).getProperty('role');
            switch(role) {
                    
                case 'formSubmit' :
                    new Form.Request('plugin-form', $$('.plugin-pane')[0]).send();
                    break;    
            
                case 'formCancel' :
                    PluginImportManager.init({
                        'plugin': $$('[name=pluginname]')[0].get('value')
                    });
                    break;
            }
        }
    }

    
    $$('[role="pluginButton"]').each(function(el){
        
        el.addEvent('click', function(ev){
            
            ev.stop();
            
            PluginImportManager.init({
                'plugin': $(this).getProperty('rel')
            });
        });
        
    });



} catch(e){ if (console && console.log) console.log(e); }
});
