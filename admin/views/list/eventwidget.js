/**
 * The javascript file for list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// Thus we dont use any templater we assume that template exist in view script
Migur.classes.listEventsManager = function(domContainer, options) {

	var obj = this;
	
	this.init = function(domContainer, options) {
		
		obj.template = 
		'<tr>'+
			'<td width="30%">{{event}}</td>'+
			'<td>{{title}}</td>'+
			'<td width="30%">{{action}}</td>'+
			'<td class="container-eventlist-rowcontrols" width="150px" align="right">'+						
				'<a href="#" data-role="control-edit" data-id="{{le_id}}" class="btn btn-small">'+
					'<i class="icon-out-2"></i>'+
					'Edit'+
				'</a>&nbsp;&nbsp;'+
				'<a href="#" data-role="control-delete" data-id="{{le_id}}" class="btn btn-small btn-danger" >'+
					'Delete'+
				'</a>'+
			'</td>'+
		'</tr>';

		obj.options = options;
		obj.domContainer = $(domContainer);
		obj.rowsContainer = $(domContainer).getElement('tbody');
		
		obj.url = migurSiteRoot + "administrator/index.php?option=com_newsletter&tmpl=component&list_id="+obj.options.listId;
		
		obj.refresh();
	}
	
	this.editItem = function(id){

		Migur.modal.show('#modal-listevent', {
			href: obj.url + "&task=listevent.edit&le_id="+id
		});
	}

	this.deleteItem = function(id){

		new Request({
			url: obj.url + "&task=listevent.delete&le_id="+id,
			onComplete: function(){
				obj.refresh();
			}
		}).send();
	}

	/**
	 * Refresh all rows in grid
	 */
	this.refresh = function() {

		new Request.JSON({
			url: obj.url + "&task=listevents.getItems",
			data: {},
			onComplete: function(res){

				var parser = new Migur.jsonResponseParser(res);
				if (parser.isError()) return;
				
				var data = parser.getData();
				obj._render(data);
				
				$(obj.rowsContainer).getElements('[data-role="control-delete"]')
					.addEvent('click', function(ev){
						ev.stop();
						obj.deleteItem($(this).getProperty('data-id'));
					})
					
				$(obj.rowsContainer).getElements('[data-role="control-edit"]')
					.addEvent('click', function(ev){
						ev.stop();
						var id = $(this).getProperty('data-id');
						obj.editItem(id);
					});
			}
		}).send();
	}

	/**
	 * Gets the data from children of domContainer Dom element 
	 * that have a data-type property
	 */
	this._render = function(data){
		
		var html = '';
		Array.each(data, function(row){
			row.title || (row.title = '---');
			row.event = Joomla.JText._('COM_NEWSLETTER_LIST_EVENT_' + row.event.toUpperCase());
			row.action = Joomla.JText._('COM_NEWSLETTER_LIST_ACTION_' + row.action.toUpperCase());
			html += obj.replace(obj.template, row);
		});
		
		$(obj.rowsContainer).set('html', html);
	}

	this.replace = function(tmpl, data){
			var pattern = new RegExp(/{{([^}]+)}}/igm);
            return tmpl.replace(pattern, function(m){ return data[arguments[1]] || m });
    };

	this.init(domContainer, options);
}


window.addEvent('domready', function() {

	Migur.app.listEventsManager = new Migur.classes.listEventsManager(
		$('eventslist-container'), 
		{ 'listId': $$('[name="list_id"]')[0].getProperty('value') }
	);

	// This method will be called when save or cancel 
	// will be invoked in popup window.
	// In that way we will get to know that we need to 
	// refresh events list
	window.popupCloseCallback = function(){
		Migur.app.listEventsManager.refresh();
	}

});
