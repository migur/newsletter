/**
 * The javascript file for list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// Thus we dont use any templater we assume that template exist in view script
Migur.classes.listEventsManager = function(domContainer, dataElement) {

	var obj = this;
	
	this.storage = [];
	
	this.init = function(domContainer, dataElement) {
		
		obj.domContainer = $(domContainer);
		obj.dataElement  = dataElement;
		obj.pkey = "le_id";
		
		var base = obj.domContainer;
		
		base.getElements('[data-role=item-new]').addEvent('click', this.showAddItem);
		base.getElements('[data-role=item-edit]').addEvent('click', this.showEditItem);
		base.getElements('[data-role=item-delete]').addEvent('click', this.deleteItem);
		base.getElements('[data-role=item-add]').addEvent('click', this.addItem);
		base.getElements('[data-role=item-apply]').addEvent('click', this.applyItem);
		base.getElements('[data-role=item-cancel]').addEvent('click', this.cancelItem);

		
		obj.storage = JSON.decode(dataElement.value);

		obj.hidePanel();
	}
	
	this.hidePanel = function(){
		obj.domContainer.getElement('[data-role=item-manage-pane]').addClass('hide');
	}

	this.showPanel = function(){
		obj.domContainer.getElement('[data-role=item-manage-pane]').removeClass('hide');
	}

	this.showAddItem = function(){
		
		var base = obj.domContainer;
		var panel = base.getElement('[data-role=item-manage-pane]');
		
		$$('[data-role="items-list"]')[0].grab(panel, 'bottom');

		panel.getElement('[data-role=item-add]').removeClass('hide');
		panel.getElement('[data-role=item-apply]').addClass('hide');
		panel.removeClass('hide');
	}

	this.addItem = function(){

		var base = obj.domContainer;
		var panel = base.getElement('[data-role=item-manage-pane]');
		var template = base.getElement('[data-role=item-template]').clone(true);

		// Get data from manage panel
		var data = obj._getPanelData(panel);
		obj.updateStorage(data);
		obj.refresh();
	}


	this.deleteItem = function(){

		var parent = $(this).getParent('[data-role=item-container]'); 
		var idx = parent.getElement('[data-type=le_id]').value;
		// Get data from manage panel
		obj.deleteFromStorage(idx);
		obj.refresh();
	}

	/**
	 * Refresh all rows in grid
	 */
	this.refresh = function() {
		
		var base = obj.domContainer;
		
		var items = base.getElement('[data-role=items-list]').getElements('[data-role=item-container]');
		
		Array.each(items, function(el){
			el.destroy();
		});
		
		Array.each(obj.storage, function(item){
			var template = base.getElement('[data-role=item-template]').clone(true);
			template.removeClass('hide');
			template.setProperty('data-role', 'item-container');
			obj._renderRow(template, item);
			base.getElements('[data-role=items-list]').grab(template, 'bottom');
		});

		obj.hidePanel();
	}


	/**
	 * Gets the data from children of domContainer Dom element 
	 * that have a data-type property
	 */
	this._getPanelData = function(domContainer){
		var dataElements = domContainer.getElements('[data-type]');
		var res = {};
		
		Array.each(dataElements, function(el){
			
			var text;

			if (el.get('tag') == 'select') {
				text = el.options[el.options.selectedindex].get('html');
			} else {
				text = el.value;
			}
		
			res[el.getProperty('data-type')] = {
				'value': el.value,
				'text':  text
			};
			
		});
		return res;
	}

	/**
	 * Gets the data from children of domContainer Dom element 
	 * that have a data-type property
	 */
	this._renderRow = function(domContainer, data){
		
		Object.each(data, function(el, key){

			var domEl = domContainer.getElement('[data-type='+key+']');
			if (domEl) {
				domEl.setProperty('data-value', el.value);
				domEl.set('html', el.text);
			}	
		});
	}
	
	this.updateStorage = function(data) {
		
		var idx = data[obj.pkey];
		
		if (!data[obj.pkey]) {
			data[obj.pkey] = Math.random() * 100000 + '-' + Math.random() * 100000;
			obj.storage.push(data);
		} else {
			obj.storage[idx] = data;
		}
		
		obj.dataElement.value = JSON.encode(obj.storage);
	}

	this.deleteFromStorage = function(idx) {
		
		Array.each(obj.storage, function(row){
			if (row[obj.pkey] == idx) {
				delete(row);
				return;
			}
		});
		obj.dataElement.value = JSON.encode(obj.storage);
	}


	this.init(domContainer, dataElement);
}


window.addEvent('domready', function() {

	Migur.app.listEventsManager = new Migur.classes.listEventsManager($('list-events-pane'), $$('[name=jform[events]]')[0]);

});
