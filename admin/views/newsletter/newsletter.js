/**
 * The javascript file for newsletter view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// Hack. Set the first tab active
Cookie.write('jpanetabs_tabs-newsletter', 0);

//TODO: Tottaly refacktoring. Create and use widgets!

window.addEvent('domready', 

function() { try {

        $('tabs-sub-container').getElements('input, textarea')
        .addEvent('focus', function(ev){
            this.addClass('focused');
        })
        .addEvent('blur',  function(ev){
            this.removeClass('focused');
        });


		new Migur.modules.htmlPane();

        /*  Main tabs -> click-handlers  */
        $$('#tabs-newsletter > [data-role="tab-header"]').addEvent('click', function (event) {

            $$('#trashcan-container ul').addClass('hide');
            if ( $(this).match('.tab-html') ) {
                $('acc-newsletter').set('styles', {
                    display:'block'
                } );
            } else {
                $('acc-newsletter').set('styles', {
                    display:'none'
                } );
            }

            if ( $(this).match('.tab-plain') ) {
                $('acc2-newsletter').set('styles', {
                    display:'block'
                } );
            } else {
                $('acc2-newsletter').set('styles', {
                    display:'none'
                } );
            }
        });
    
        $$('#tabs-newsletter > li')[0].fireEvent('click');

        /* Type of letter */
        if (dataStorage.newsletter.type_changeable !== undefined && !dataStorage.newsletter.type_changeable) {
            $$('[name=jform[type]]').each(function(el){
                el.onclick = function(){return false;}
            });
        }

        /* bind data to Smtp Profile select*/
        $('jform_smtp_profile_id').store('optionsData', smtpProfiles);

        /* "smtp" drop-down box -> change-handler */
        $('jform_smtp_profile_id').addEvent('change', function () {
            var inputs = $$('.smtp-dependency');

			var sets = this.retrieve('optionsData');
			var id = $(this).get('value');

			var vals = {
				from_name: '',
				from_email: '',
				reply_to_name:'',
				reply_to_email:''
			};

			for (var i=0; i < sets.length; i++) {
				if (sets[i].smtp_profile_id == id) {
					vals = sets[i];
				}
			}
				
            if( id != 0 && vals.is_joomla != 1) {
            
                $('jform_params_newsletter_from_name').set('value', vals.from_name);
                $('jform_params_newsletter_from_email').set('value', vals.from_email);
                $('jform_params_newsletter_to_name').set('value', vals.reply_to_name);
                $('jform_params_newsletter_to_email').set('value', vals.reply_to_email);
				
                inputs.addEvent('keydown', function(){
                    return false;
                });
				
                inputs.setProperty('readonly', true);
				
            } else {
                inputs.removeProperty('readonly');
                inputs.removeEvents('keydown');
                inputs.each(function(el){
					var val = el.retrieve('value');
					if (val) {
	                    el.set('value', val);
					}	
                });
            }
        });


        /* "smtp settings" inputs -> keyup-handler */
        $$('.smtp-dependency').addEvent('keyup', function () {
            $(this).store('value', $(this).get('value'));
        });

		$('jform_smtp_profile_id').fireEvent('change');

        /* "Clear Profile" button -> change-handler */
        $('button-newsletter-clear-profile').addEvent('click', function () {
            $('jform_smtp_profile_id').set('value', '0').fireEvent('change');
        });

        /* "Subject" input -> keyup-handler */
		 Migur.createWidget('jform_alias', {

			setup: function(){
				
				// Adding handler to a DOM element
				this.domEl.addEvent('keyup', function(){
					var wdgt = Migur.getWidget('jform_alias');
					wdgt.update();
				});
				
				this.update();
			},
			
			update: function(data){

				// If we have new data then set it into control
				if (data) {
					this.domEl.setProperty('value', data);
				}

				// Fill DATA if not provided to refresh the link
				if (!data) {
					data = this.domEl.getProperty('value');
				}
				
	            var link = $('link-website');
				var linkPrompt = $('link-website-prompt');
				
				if (data == '') {
					link.addClass('hide');
					linkPrompt.removeClass('hide');
				} else {
					var val = migurSiteRoot + link.getProperty('rel').replace('%s', data);
					link.removeClass('hide');
					linkPrompt.addClass('hide');
					link.set('text', val);
					link.setProperty('href', val);
				}
			}
		 });	


		new Migur.modules.autosaver;

		
        $('jform_newsletter_preview_email').addEvent('focus', function(){
            //TODO: Add to translations
            if (this.value == Joomla.JText._('EMAILS',"Emails...")) this.value = '';
            $(this).setStyle('color', 'black');
        })
        .addEvent('blur', function(){
            if (this.value == '') {
                this.value = Joomla.JText._("EMAILS","Emails...");
                $(this).setStyle('color', 'grey');
            }
        })
        $('jform_newsletter_preview_email').fireEvent('blur');


    $('templates-container').set( 'value', $$('[name=jform[t_style_id]]')[0].get('value') );
    $('templates-container').fireEvent('change');



new Migur.modules.plain;

// Create autocompleter module
//var autocompleterModule = new Migur.modules.autocompleter;

// Create preview module
new Migur.modules.preview({
	//'autocompleter': autocompleterModule
});

if (isNew == 1) {

	var guide = new Migur.modules.guide;
	
    setTimeout(
		function(){
			guide.start.apply(guide.migurGuide)
		}, 
		'1000'
	);
		
}





} catch(e){
    if (console && console.log) console.log(e);
}

});
