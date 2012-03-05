window.addEvent('domready', function() {try {

	Migur.maintenanceManager = {

		request: null,

		sequence: [],
		
		pointer: 0,

		add: function(func){
			this.sequence.push(func);
			return this;
		},

		start: function(){
			this.pointer = 0;
			this.sequence[0](this.onCheckComplete);
		},


		onCheckComplete: function(){
			var man = Migur.maintenanceManager
			man.pointer++;
			if(typeof man.sequence[man.pointer] == 'function') {
				man.sequence[man.pointer](man.onCheckComplete);
			}	
		}
	}

	Migur.maintenanceManager
		.add(function(){
			var w = Migur.getWidget('environment-check-pane');
			w.refresh(Migur.maintenanceManager.onCheckComplete); })
		
		.add(function(){
			var w = Migur.getWidget('db-check-pane');
			w.refresh(Migur.maintenanceManager.onCheckComplete); })
		
		.add(function(){
			var w = Migur.getWidget('smtp-check-pane');
			w.refresh(Migur.maintenanceManager.onCheckComplete); })

		.add(function(){
			var w = Migur.getWidget('mailbox-check-pane');
			w.refresh(Migur.maintenanceManager.onCheckComplete); });



// Environment widget
	Migur.createWidget('environment-check-pane', { 
		options: { 
			url: '?option=com_newsletter&task=maintenance.checkenvironment',
			usePreloader: true	
		}}, Migur.widget.ajaxChecker);



// DB widget
	Migur.createWidget('db-check-pane', { 
		options: { 
			url: '?option=com_newsletter&task=maintenance.checkdb',
			usePreloader: true
		}}, Migur.widget.ajaxChecker);



// SMTP widget
	Migur.createWidget('smtp-check-pane', { 
		options: { 
			url: '?option=com_newsletter&task=maintenance.checksmtps',
			usePreloader: true
		}}, Migur.widget.ajaxChecker);


// Mailbox widget
	Migur.createWidget('mailbox-check-pane', { 
		options: { 
			url: '?option=com_newsletter&task=maintenance.checkmailboxes',
			usePreloader: true
		}}, Migur.widget.ajaxChecker);


	$('environment-check-start').addEvent('click', function(){
		Migur.maintenanceManager.start();
	});
	

} catch(e) {console.log(e);}});












