
window.addEvent('domready', function() {
try {	
	
	
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
	
	
	$$('.item .edit').addEvent('click', function(ev){
		
		ev.stop();

		var form = $('automailingitemsForm');
		
		var id = $(this).getParent('.item').getElements('[name=cid[]]')[0].getProperty('value');
		var href = "index.php?option=com_newsletter&task=automailingitem.edit&tmpl=component&series_id="+id;
		
		SqueezeBox.open(href, {
			handler: 'iframe',
			size: {
				x: 350,
				y: 175
			}
		});
	});
	
	
	$$('#automailing-cancel a')
	.removeProperty('onclick')
	.addEvent('click', function(){
		if (window && window.parent && window.parent.SqueezeBox) {
			window.parent.SqueezeBox.close();
		}
		return false;
	});
	
	
	
	if (isNew == 0) {
		$('jform_automailing_type').setProperty('readonly', true);
	}
	
	
} catch(e) {}	
});