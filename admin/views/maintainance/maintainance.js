window.addEvent('domready', function() {try {

	Migur.maintainanceManager = {

		request: null,

		sequence: [],
		
		pointer: 0,

		add: function(func){
			this.sequence.push(func);
			return this;
		},

		start: function(){
			this.pointer = 0;
			this.sequence[0](this.onCheckComplete, 'refresh');
		},


		onCheckComplete: function(){
			var man = Migur.maintainanceManager
			man.pointer++;
			if(typeof man.sequence[man.pointer] == 'function') {
				
				man.sequence[man.pointer](man.onCheckComplete, 'refresh');
				
			}
		},
		
		getReport: function(){
			
			var resp = [];
			Array.each(this.sequence, function(func){
				
				resp.push(func(null, 'getState'));
			});
			
			return resp;
		}
		
	}

// Environment widget
	Migur.createWidget('environment-check-pane', { 
		options: { 
			url: '?option=com_newsletter&task=maintainance.checkenvironment',
			usePreloader: true,
			title: 'Environment checks'
		}}, Migur.widget.ajaxChecker);



// DB widget
	Migur.createWidget('db-check-pane', { 
		options: { 
			url: '?option=com_newsletter&task=maintainance.checkdb',
			usePreloader: true,
			title: 'DB checks'
		}}, Migur.widget.ajaxChecker);



// SMTP widget
	Migur.createWidget('smtp-check-pane', { 
		options: { 
			url: '?option=com_newsletter&task=maintainance.checksmtps',
			usePreloader: true,
			title: 'SMTP profiles checks'
		}}, Migur.widget.ajaxChecker);


// Mailbox widget
	Migur.createWidget('mailbox-check-pane', { 
		options: { 
			url: '?option=com_newsletter&task=maintainance.checkmailboxes',
			usePreloader: true,
			title: 'Mailbox profiles checks'
		}}, Migur.widget.ajaxChecker);

// License widget
	Migur.createWidget('license-check-pane', { 
		options: { 
			url: '?option=com_newsletter&task=maintainance.checklicense',
			usePreloader: true,
			title: 'License checks'
		}}, Migur.widget.ajaxChecker);

	Migur.maintainanceManager
	
		.add(function(callback, type){
			var w = Migur.getWidget('environment-check-pane');
			return w[type](callback);})
		
		.add(function(callback, type){
			var w = Migur.getWidget('db-check-pane');
			return w[type](callback);})
		
		.add(function(callback, type){
			var w = Migur.getWidget('smtp-check-pane');
			return w[type](callback);})
		
		.add(function(callback, type){
			var w = Migur.getWidget('mailbox-check-pane');
			return w[type](callback);})

		.add(function(callback, type){
			var w = Migur.getWidget('license-check-pane');
			return w[type](callback);})

		.add(function(){
			$('maintainance-get-report').setStyle('display', 'block');
		});


	$('maintainance-check-start').addEvent('click', function(){
		Migur.maintainanceManager.start();
	});


	$('maintainance-get-report').addEvent('click', function(){
		var data = Migur.maintainanceManager.getReport();
		$$('[name=jform[data]]')[0].setProperty('value', JSON.encode(data));
		$$('[name=adminForm]')[0].submit();
	});


} catch(e) {
    if (console && console.log) console.log(e);
}});
