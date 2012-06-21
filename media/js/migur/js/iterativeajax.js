if (typeof Migur == 'undefined') Migur = {};

Migur.iterativeAjax = function(options) {
	
	this.url = null;
	this.form = null;
	this.data = {};
	this.method = 'post';
	this.messagePath   = null;
	this.preloaderPath = null;
	this.isIterative = true;

	this.init = function(options){
		
		Object.merge(this, options);
	}
	
	this.start = function(){
		
		this.data.step = 0;
		
		this.data.offset = 0;
		
		if (!this.data.limit)
			this.data.limit = 1000;
		
		this.hidePreloader();
		
		this.showMessage(Joomla.JText._('PLEASE_WAIT', 'Please wait') + '...');

		this.step();
	};
	
	this.step = function(){

		this.data.step++;

		var $this = this;

		new Request({
			url: this.url,
			method: this.method,
			data: this.data,
			onComplete: function(){
				$this.onStepComplete.apply($this, arguments);
			}
		}).send();
		
		this.showPreloader();
	};
	
	this.onStepComplete = function(result){

		this.hidePreloader();

		var parser = new Migur.jsonResponseParser();
		parser.setResponse(result);
		
		if (parser.isError()) {
			return this.onError(parser.getMessagesAsList(
				Joomla.JText._('AN_UNKNOWN_ERROR_OCCURED', 'An unknown error occured!')
			));
		}
		
		var data = parser.getData();
		
		this.showMessage(data.total + ' ' + Joomla.JText._('ITEMS_PROCESSED', 'items processed') + '...');
		
		if (data.fetched > 0 && this.isIterative) {
			// Let server decide about offset 
			this.data.offset = null;
			return this.step();
		}

		this.hideMessage();

		return this.onComplete(parser.getMessages(), data);
	};

	this.onError = function(text) {

		this.hidePreloader();
		
		this.hideMessage();

		return this.showAlert(text);
	};

	this.onComplete = function(messages, data) {

		return this.showAlert(messages, data);
	};

	this.showAlert = function() {

		var text = '';
		
		Array.each(arguments, function(el){
	
			if (typeof el == 'string') {
				text += "\n" + el;
			}
			
			if (typeof el == 'object') {
				Object.each(el, function(item, name){
					
					if (name.length > 1) {
						text += "\n" + name + ": " + item;
					} else {
						text += "\n" + item;
					}	
				});
			}
		});

		alert(text);
	};
	
	this.showPreloader = function(){
		if (this.preloaderPath && $$(this.preloaderPath).length > 0) {
			$$(this.preloaderPath)[0].addClass('preloader');
		}
	};
	
	this.hidePreloader = function(){
		if (this.preloaderPath && $$(this.preloaderPath).length > 0) {
			$$(this.preloaderPath)[0].removeClass('preloader');
		}
	};
	
	this.showMessage = function(text){
		if (this.messagePath && $$(this.messagePath).length > 0) {
			$$(this.messagePath)[0].set('html', text);
		}
	};
	
	this.hideMessage = function(){
		if (this.messagePath && $$(this.messagePath).length > 0) {
			$$(this.messagePath)[0].set('html', '');
		}
	};
	
	// Do a constructor
	this.init(options);
}
