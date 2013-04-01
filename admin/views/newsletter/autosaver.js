
Migur.define("autosaver", function() {

	var docstateElement = $$('#toolbar-docstate > span')[0];

	/* Start the autosaver */
	var autosaver = new Migur.autosaver({
		options: {
			repeat: true,
			timeout: 3000,
			observable: $('tabs-sub-container'),
			url: '?option=com_newsletter&context=json'
		},

		beforeSend: function(){
				$$('form [name=task]').set('value','newsletter.apply');
		},

		getter: function(){

			var htmlConstructor = Migur.getWidget('html-area');
			var htmlTpl = htmlConstructor.parse();
			var inputs  = $('tabs-sub-container').toQueryString();

			// Get the all data from all plugins!
			var plugins = [];
			$$('.plugin').each(function(el) {
				plugins.push( Migur.getWidget(el).get() );
			});
			var obj = new Hash(inputs.parseQueryString());

			obj["jform[newsletter_preview_email]"] = "";
			obj["jform[htmlTpl]"] = JSON.encode(htmlTpl);
			obj["jform[plugins]"] = JSON.encode(plugins);

			$$("[name=jform[htmlTpl]]")[0].setProperty('value', obj["jform[htmlTpl]"]);
			$$("[name=jform[plugins]]")[0].setProperty('value', obj["jform[plugins]"]);

			return obj.toQueryString();
		},

		// On success
		onSuccess: function(res){
			
			var parser = new Migur.jsonResponseParser(res);

			var data = parser.getData();

			if (parser.isError()) {
				
				var switcher = Migur.getWidget('autosaver-switch');
				if (autosaver.messageCannotsave === undefined || switcher.data == 'off') {
					alert( parser.getMessagesAsList(Joomla.JText._('AN_UNKNOWN_ERROR_OCCURED', 'An unknown error occured!')) );
					autosaver.messageCannotsave = true;
				}	
				return;	
			}

			if (data.newsletter_id > 0) {

				// Update value
				$$('[name=newsletter_id]').set('value', data.newsletter_id);

				// Update alias and newsletter link 
				Migur.getWidget('jform_alias').update(data.alias);

				docstateElement
					.set('html', Joomla.JText._('ALL_CHANGES_SAVED', 'All changes saved'))
					.setStyle('color', 'black');

				//Migur.getWidget('autosaver-switch').render();
				autosaver.options.observState = autosaver.getter();
			}
		},

		controller: function(data) {
			// If the HTML constructor is not ready yet the we cant get data from it...
			var htmlConstructor = Migur.getWidget('html-area');
			if (!htmlConstructor.initialised) {
				return false;
			}

			var isChanged = this.isChanged(data);
			
			this.setMessage(isChanged);
			
			var form = $$('form.form-validate')[0];
			var res = document.formvalidator.isValid(form);

			Migur.validator.tabIndicator(
				'#tabs-newsletter',
				'li a',
				'tab-invalid',
				'.invalid'
				);

			if (!res) {
				return false;
			}
			
			return isChanged;
		}, 

		setMessage: function(isChanged) {
			if (isChanged) {
				docstateElement
					.set('html', Joomla.JText._('CHANGES_ARENT_SAVED', 'Changes aren\'t saved'))
					.setStyle('color', 'red');
			} else {
				docstateElement
					.set('html', Joomla.JText._('ALL_CHANGES_SAVED', 'All changes saved'))
					.setStyle('color', 'black');
			}

		},

		update: function(){
			var data = this.getter();
	        this.setMessage(this.isChanged(data));
		}	

	});

	Migur.app.autosaver = autosaver;

	$$('input, select, textarea').addEvent('blur', function(){
		Migur.validator.tabIndicator(
			'#tabs-sub-container',
			'span h3 a',
			'tab-invalid',
			'.invalid'
			);
	});


	$$('#toolbar-apply > *')[0].addEvent('click', function(ev){

		ev.stop();

		var res = autosaver.send(false, 'use controller');

		if (res) {
			// Hide the message (0008255: Ability to configure autosave)
			autosaver.prompt = 'already';

			if (typeof(autosaver.prompt) == 'undefined') {
				autosaver.prompt = 'already';
				var conf = confirm(Joomla.JText._('DO_YOU_WANT_TO_TURN_ON_AUTO_SAVE_INSTEAD_QM','Do you want to turn on "Auto save" instead?'));
				if (conf) {
					Migur.getWidget('autosaver-switch').turnOn();
				}
			}

		} else {
			alert(Joomla.JText._(
				'AN_ERROR_OCCURED_DURING_SAVE_PLEASE_TRY_TURNING_ON_AUTOSAVE_INSTEAD',
				'An error occured during save, please try turning on autosave instead.'
			));
		}
	});


	/* Autosaver switch onclick-handler */
	$$('#toolbar-autosaver > *')[0].setProperty('id', 'autosaver-switch');

	var control = Migur.createWidget('autosaver-switch', {

		setup: function(){
			if (typeof (comParams.autosaver_enabled) != 'undefined' && comParams.autosaver_enabled == '1') {
				this.data = 'on';
				this.turnOn();
			} else {
				this.data = 'off';
				this.turnOff();
			}
		//this.data = null;
		},

//		render: function() {
//			$(this.domEl).set({
//				html:
//				'<span id="autosaver-icon"></span>'+
//			'<span id="content-state"></span>'
//			});
//			this.update('saved');
//			(this.data == 'on')? this.turnOn() : this.turnOff();
//		},

		turnOn: function() {
			var el = $(this.domEl);
			el.getElements('#autosaver-icon')[0]
				.removeClass('icon-cancel')
				.addClass('icon-save');
			el.getElements('#content-state')[0].
				set('html', Joomla.JText._('AUTOSAVE_IS_ON', 'Autosave is on'));
			
			autosaver.start();
			this.data = 'on';
		},

		turnOff: function() {
			var el = $(this.domEl);
			el.getElements('#autosaver-icon')[0]
				.removeClass('icon-save')
				.addClass('icon-cancel');
			el.getElements('#content-state')[0].
				set('html', Joomla.JText._('AUTOSAVE_IS_OFF', 'Autosave is off'));

			autosaver.stop();
			this.data = 'off';
		},
		
		change: function() {
			
			if (this.data == 'off') {
				this.turnOn();
			}else {
				this.turnOff();
			}
		},
		
		events: {
			'click': function(){
				Migur.getWidget(this).change();
				return false;
			}
		}
	});


	/* Unsaved data warning handler */
	$('tabs').addEvent('change', function() {
		autosaver.update();
	});

	$$('#tabs [type="radio"]').addEvent('click', function() {
		autosaver.update();
	});

	$$('#tabs textarea').addEvent('keyup', function() {
		autosaver.update();
	});

	return autosaver;

});