Migur.widget.ajaxChecker = new Class({

	Implements: Migur.widget,

	options: {},
	
	data: {},

	setup: function()
	{
		var widget = this;
		
		if(this.domEl.getElements('.refresh-control').length > 0) {
			this.domEl.getElements('.refresh-control')[0].addEvent('click', 
				function(ev){
					ev.stop();
					widget.refresh();
				});
		}
		
		this.data.title = this.options.title;
	},	



	refresh: function(onCompleteCallback)
	{
		$(this.domEl).setStyle('display', 'block');

		if (typeof onCompleteCallback == 'function') {
			this.onCompleteCallback = onCompleteCallback;
		}	
		
		this.data.notifications = [];
		
		this.clearNotifications();
		
		this.setStatus(Joomla.JText._('CHECKING', 'Checking') + '...', 'neutral');
		
		this.load(
			this.options.url,
			null,
			this.onRefreshComplete);
	},



	onRefreshComplete: function(res)
	{
		var widget = this;
		var parser = new Migur.jsonResponseParser();
		parser.setResponse(res);
		
		if (parser.getData().length > 0) {
			
			Array.each(parser.getData(), function(row){
				widget.addNotification(row.text, row.type);
			});	
			
		} else {
			this.addNotification(Joomla.JText._('ERROR_OCCURED', 'An error occured'), false);
		}
		
		if (typeof this.onCompleteCallback == 'function') {
			this.onCompleteCallback(this.data);
		}	
	},



    load: function(url, data, completeCallback){

        var widget = this;

        widget.transport = new Request({
            url: url,
            data: data,

            onComplete: function(res){
				
				widget.switchPreloader(false);
				
                delete widget.transport;

				widget.onLoadComplete.call(widget, res);

				if (typeof completeCallback == 'function') {
					completeCallback.call(widget, res);
				}
            }
        }).send();
		
		if (this.options.usePreloader){
			this.switchPreloader(true);
		}
    },



	onLoadComplete: function(res)
	{
		var parser = new Migur.jsonResponseParser();
		parser.setResponse(res);
		
		if(parser.isError()) {
			this.setStatus(Joomla.JText._('CHECK_FAILED', 'Check failed'), 'error');
			this.data.status = false;
			this.data.data = [];
			return;
		}	
		
		this.data.status = true;
		this.data.data = parser.getData();
		
		this.setStatus(Joomla.JText._('CHECK_COMPLETED', 'Check completed'), 'ok');
	},



	switchPreloader: function(onOff)
	{
		var el = $(this.domEl).getElements('.preloader-container');
		if (el.length > 0) {
			if (onOff == true) {
				el[0].addClass('preloader');
			} else {
				el[0].removeClass('preloader');
			}	
		}
	},



	setStatus: function(text, mood) 
	{
		var el = $(this.domEl).getElements('.status-verbal');
		if(el.length == 0) {
			return false;
		} 
		
		el[0].set('html', text);
		
		if (mood) {
			el.set('class', 'status-verbal');
			el.addClass(mood);
		}
		
		return true;
	},



	addNotification: function(data, type) 
	{
		var cls = (type)? 'check check-message' : 'check check-error'
		
		
		var wrp = new Element('div', {
			'class': cls,
			'html': 
				'<span class="notification-text">'+data+'</span>'+
				'<span class="notifiacation-status">'+
					((type)? Joomla.JText._('OK') : Joomla.JText._('FAIL'))+
				'</span>'	
		});
		
		var cont = $(this.domEl).getElements('.notifications')[0];
		cont.grab(wrp, 'bottom');
		
		return true;
	},

	clearNotifications: function() 
	{
		var cont = $(this.domEl).getElements('.notifications')[0];
		cont.set('html', '');
		return true;
	},
	
	
	getState: function() 
	{
		return {
			'check':  this.data.title,
			'status': this.data.status,
			'data':   this.data.data
		};
	}

});