/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {
    try {

        /**
     * Create the slide panel for filter options
     */
        $$('.filter-panel').each(function(el){

            //TODO: Move to widgets

            var id = el.getProperty('id');
			
            var control = new Element('a', {
                href: '#',
                //TODO: It must be a multilingual
                html: Joomla.JText._('SEARCH_AND_FILTERS', 'Search & Filters') + '<span data-role="ctrl-icon"></span>',
                events: {
                    click: function(){
                        var controlId = $(this).getParent('[data-role="ctrl-container"]').getProperty('id');
                        var id = controlId.replace('-control', '');
						var arrow;
						var isOpen = Cookie.read(id)
						
						if (isOpen == '1') {
							$(id).setStyle('overflow', 'hidden');
							$(id).tween('height', 0);
							arrow = 'icon-16-slide-down';
						} else {
							var child = $(id).getChildren('[data-role="panel-container-inner"]');
							child.setStyle('overflow', 'hidden');
							var hght = child.getStyle('height');
							child.setStyle('overflow', 'visible');
							
							var fx = new Fx.Tween(id, {'link': 'chain'});
							fx.start('height', hght);
							fx.start('overflow', 'visible');
							arrow = 'icon-16-slide-up';
						}
						
                        $(this).getChildren('[data-role="ctrl-icon"]').set('class', arrow);
                        Cookie.write(id, (isOpen == '1')? '0' : '1');
                        return false;
                    }
                }
            });

            $(id + '-control').grab(control);

            var arrow;
            if(Cookie.read(id) == '1' || el.hasClass('opened')) {
				var hght = $(id).getChildren('[data-role="panel-container-inner"]')[0].getStyle('height');
				el.setStyle('height', hght);
				el.setStyle('overflow', 'visible');
                arrow = 'icon-16-slide-up';
            } else {
				el.setStyle('height', 0);
				el.setStyle('overflow', 'hidden');
                arrow = 'icon-16-slide-down';
            }
            $(control).getChildren('[data-role="ctrl-icon"]')[0].set('class', arrow);
        });

    } catch(e){
        if (console && console.log) console.log(e);
    }
});
