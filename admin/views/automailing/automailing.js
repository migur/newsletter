
window.addEvent('domready', function() {

	// Fix for IE8. Because IE triggers native submit when
	// clicking on <button> that is placed INSIDE of a form.
	// So we need to prevent that default unwanted action.
	$$('form button').each(function(el){
	var onClick = el.getProperty('onclick');
	if (onClick) el.setProperty('onclick', 'event.returnValue = false; ' + onClick + '; return false;');
	})

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


	var toggle = function() {
			var value = $(this).get('value');
			var disp = (value=='all')? 'none' : 'block';
			$('scope-container').setStyle('display', disp);
			$('jf_scope').set('value', value);
	}

	if($$('#jform_scope input').length > 0) {

		$$('#jform_scope input').addEvents({
			'click': function() {
				return toggle.apply(this);
			},
			/* change is needed for IE8 */
			'change': function(){
				return toggle.apply(this);
			}
		});
/*
		$('jform_scope').addEvent('click', function(ev){

			console.log(ev.target, ev.target.target);
			if (!ev.target.getProperty('id')) return;

			var el = ev.target;

			var value = el.get('value');
			var disp = (value=='all')? 'none' : 'block';
			$('scope-container').setStyle('display', disp);
			$('jf_scope').set('value', value);
		});
*/
		var checked = $('jform_scope0').getProperty('checked')
		var disp = checked? 'none' : 'block';
		$('scope-container').setStyle('display', disp);
	}
});
