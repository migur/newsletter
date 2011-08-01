/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

/* TODO: Romove the "translates". Use JText instead */
var translates = {}
translates.search = 'Search...';

window.addEvent('domready', function() {
    
    /**
     * Add the "Search..." to search inputs
     */
    $$('.migur-search').each(function(el) {

        el.addEvent('focus', function(){
            if (this.value == translates.search) this.value = ''
            $(this).setStyle('color', 'black');
        })
        .addEvent('blur', function(){
            if (this.value == '') {
                this.value = translates.search;
                $(this).setStyle('color', 'grey');
            }
        })
    });

    $$('form').each(function(el) {

        el.nativeSubmit = el.submit;
        el.submit = function(){

            $$('.migur-search').each(function(el){
                el.fireEvent('focus');
            });

            el.nativeSubmit();
        }
        
        el.addEvent('submit', el.submit);
    });

    $$('.migur-search').each(function(el) {
        el.fireEvent('focus');
        el.fireEvent('blur');
    });


});
