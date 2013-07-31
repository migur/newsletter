/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Migur.classes.storage = function(options) {

	this.data = [];

	this.pkName = null;

	// Url to fetch data
	this.getUrl = null;
	
	// Url to post data on insert/update of storage data.
	this.updateUrl = null;

	this.singleUrl = false;

	this.syncOnUpdate = false;
	
	this.syncOnUpdateMode = 'dirty'; // 'all', 'dirty', null
	
	this._stack = [];
	
	this.setCollection = function(data, isDirty, mode) 
	{
		if (!data) throw 'Storage.fill: data is not defined.';
		
		if (isDirty === undefined) isDirty = false;
		if (mode    === undefined) mode = 'full';
		
		this._fillLocalStorage(data, isDirty, mode);
		return true;
	}


	this.getItemById = function(id) 
	{
		if (!id) throw 'Storage.getItemById: id is not defined.';
		
		for (var i=0; i < this.data.length; i++) {
			if (this.data[i][this.pkName] == id) return this.data[i];
		}
		
		return null;
	}


	this.insertItem = function(rowData)
	{
		if (!this._insertItemLocal(rowData)) return false;
		
		if (this.syncOnUpdate) {
			this._store('update');
		}	
	}

	this._insertItemLocal = function(rowData)
	{
		rowData['_isDirty'] = true;
		rowData[this.pkName] = Math.rand() * -100000;
		this.data.push(rowData);
	}

	this.updateItem = function(rowData)
	{
		if (!this._updateItemLocal(rowData)) return false;
		
		if (this.syncOnUpdate) {
			this._store('update');
		}	
	}

	this.fetchCollection = function() 
	{
		var url = this._getUrl('get');
		this._sync(url, 'get', {}, 'onFetchComplete');
	}

	this._updateItemLocal = function(rowData)
	{
		var row = this.getItemById(rowData[this.pkName]);
		if (!row) return false;
		
		for (var field in rowData) {
			row[field] = rowData[field];
		}
		
		row['_isDirty'] = true;
		return true;
	}	

	this.store = function() 
	{
		this._store('onStoreComplete');
	}

	this._store = function(event)
	{
		var url = this._getUrl('update');
		var mode = this.syncOnUpdateMode;
		var data = [];
		
		if (mode == 'dirty') {
			for(var i=0; i < this.data.length; i++) {
				if (this.data[i]['_isDirty']) {
					data.push(this.data[i]);
				}
			}
		} else {
			data = this.data;
		}
		
		this._sync(url, 'post', data, event);
	}

	this._sync = function(url, method, data, event)
	{
		var obj = this;
		
		new Request({
			'data': JSON.encode(data),
			'url': url,
			'method': method,
			onComplete: function(res){
				
				res = eval('('+res+')');
				obj._fillLocalStorage(res.data, res.mode);
				
				obj._trigger(event, res.data);
			}
		}).send();
		
	}

	this._fillLocalStorage = function(data, mode)
	{
		// If server answer RESET then all was ok and 
		// it give us fresh data from storage. 
		// So we must replace our data with this data
		if (mode == 'full') {
			this.data = data;
		}
		
		// If server answer RESET then all was ok and 
		// it give us IDs of a temp items we created earlier. 
		// So we must replace temp IDs in our data with these real IDs.
		if (mode == 'partial') {
			
			for(var remoteRow in data) {
				var localRow = this.getItemById(remoteRow['_tmpId']);
				if (localRow) {
					localRow = remoteRow;
					delete localRow['_tmpId'];
					localRow['_isDirty'] = false;
				}
			}
		}
	}
	
	this.deleteItemById = function(id)
	{
		var item = this.getItemById(id);
		if (item) {
			
			// If item is local (have a temp ID) then we can remove it immediately
			if (item[this.pkName] < 0) {
				delete item;
				return;
			}
			
			item['_isDirty'] = true;
			item['_isDeleted'] = true;
			
			if (this.syncOnUpdate) {
				this._store('update');
			}	
		}
	}

	this._getUrl = function(type) 
	{
		if (this.singleUrl == true) {
			return this.getUrl;
		}
		
		return this[type+'Url'];
	}
	
	this._trigger = function(eventName, data)
	{
		if (this._stack[eventName] === undefined) this._stack[eventName] = [];

		var obj = this;
		Array.each(this._stack[eventName], function(item)
		{
			if (typeof item == 'function') {
				item.call(obj, data);
			}
		});
	}
	
	this.bind = function(eventName, functionPtr)
	{
		if (this._stack[eventName] === undefined) this._stack[eventName] = [];
		
		this._stack[eventName].push(functionPtr)
	}
	
	this._init = function(options) {
		
		// Populate the object with options
		for (var i in options) {
			this[i] = options[i];
		}
		
		if (!this.pkName) {
			throw 'Storage._init: pkName is not provided';
		}
	}

	this._init(options);
}

/*
var st = new Migur.classes.storage({
	getUrl: '/dev/joomla/administrator/index.php?option=com_newsletter&view=list&tmpl=component&layout=jsontest&list_id=2',
	pkName: 'le_id',
	singleUrl: true
});
st.bind('onFetchComplete',function(){
//        console.log(this.data);
    var item = st.getItemById('1');
    var item2 = st.getItemById('2');
    item.action = 'ololo';
    item2.action = 'trololo';
    st.updateItem(item);
    st.updateItem(item2);
    st.store();
});
st.fetchCollection();

*/