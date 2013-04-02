
window.addEvent('domready', function() {
	
	
	// Functionality for default.php layout
	if (isNew == 0) {

		$("form-automailing").grab(
			new Element('input', {
				'type' : 'hidden',
				'value': automailing.automailing_event,
				'name' : 'jform[automailing_event]'
			}));

		if ($("jform_automailing_event")) {
			$("jform_automailing_event").setProperty('disabled', true);
		}	
		
	} else {
		
		$$('#toolbar-cancel button')[0]
			.removeProperty('onclick')
			.addEvent('click', function(ev){
				ev.stop();
				Migur.closeModal();
			})
	}
	
	/**
	 * Event handler for close buttons of series' items
	 */
	$$('.item .close').addEvent('click', function(ev){
		
		ev.stop();
		
		if (!confirm(Joomla.JText._('ARE_YOU_SURE_QM', 'Are you sure?'))) {
			return false;
		}
		
		var form = $('automailingitemsForm');
		
		form.getElements('[name=task]')[0].setProperty('value', 'automailing.unbindItem');

		var id = $(this).getParent('.item').getElements('[name=cid[]]')[0].getProperty('value');
		form.getElements('[name="item_id"]')[0].setProperty('value', id);
		
		form.submit();
	});
	
	
	if($$('#jform_scope input').length > 0) {
	
		$$('#jform_scope input').addEvent('click', function(){
			var value = $(this).get('value');
			$('scope-container').setStyle('display', (value=='all')? 'none' : 'block');
			$('jf_scope').set('value', value);
		});

		var checked = $('jform_scope0').getProperty('checked')
		$('scope-container').setStyle('display', checked? 'none' : 'block');
	}
});