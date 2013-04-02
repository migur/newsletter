/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {

    /**
     * Create the slide message from the standard J! one.
     * TODO: Refactor to widget
     */

// For J!2.0
	var smDom = $('system-message');
    if (smDom) {

        smDom.setStyles({
            position: 'absolute',
            width:    '100%',
            height:   '50px',
            margin:   '0px',
            padding:  '0px',
            zIndex:   1000,
            cursor:   'hand'
        });


        var sm = new Fx.Tween('system-message', {
            link: 'ignore',
            property: 'margin-top'
        });

        smDom.store('slide', sm);

        smDom.addEvent('click', function(){
            $('system-message').retrieve('slide')
                .start( (-1) * parseInt(smDom.getStyle('height')), 0 );
            setTimeout(function(){
                var el = $('system-message');
                el.retrieve('slide')
                    .start( 0, (-1) * parseInt(el.getStyle('height'))-20);
            }, 4000);
        });

        smDom.fireEvent('click');
    }


// For J!3.0
	if ($$('body.component #system-message-container').length) {
		
		var msgContainer = $$('body.component #system-message-container')[0];

		msgContainer.setStyles({
			'position': 'absolute',
			'width': '100%'
		});

		var hidding = false;

		var hideMessages = function(){
			msgContainer.set('html', '');
			hidding = false;
		}


		msgContainer.addEvent('click', hideMessages);
		
		// Poll for changes in messages container
		setInterval(function(){
			
			if(msgContainer.get('html') != '' && hidding == false) {
				// If container is not empty then clear it after small pause
				hidding = true;
				setTimeout(hideMessages, 3000);
			}
			
		}, 500)
	}
	

});
