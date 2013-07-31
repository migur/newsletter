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
            var slide = new Fx.Slide(id);
            el.store('slide', slide);
            var control = new Element('a', {
                href: '#',
                //TODO: It must be a multilingual
                html: Joomla.JText._('SEARCH_AND_FILTERS', 'Search & Filters') + '<span></span>',
                events: {
                    click: function(){
                        var controlId = $(this).getParent().getProperty('id');
                        var id = controlId.replace('-control', '');
                        var slide = $(id).retrieve('slide');
                        slide.toggle();
                        var arrow = (slide.open)? 'icon-16-slide-down' : 'icon-16-slide-up';
                        $(this).getChildren('span').set('class', arrow);
                        Cookie.write(id, (slide.open)? '0' : '1');
                        return false;
                    }
                }
            });

            $(id + '-control').grab(control);

            var arrow;
            if(Cookie.read(id) == '1' || el.hasClass('opened')) {
                slide.show();
                arrow = 'icon-16-slide-up';
            } else {
                slide.hide();
                arrow = 'icon-16-slide-down';
            }
            $(control).getChildren('span').set('class', arrow);
        });

    } catch(e){
        if (console && console.log) console.log(e);
    }
});
