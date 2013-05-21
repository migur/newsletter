function jInsertFieldValue(value, id) {

	if (typeof Migur.moodialogs[0] != 'undefined') {
		Migur.moodialogs[0].data.params.img = migurSiteRoot + '/' + value;
		Migur.moodialogs[0].task = 'apply';
		Migur.moodialogs[0].close();
	}

	if ($('tabs') != null) {
		$('tabs').fireEvent(
			'mediaselected',
			{id: id, value: value}
		);
	}
}


Migur.define('htmlPane', function(){

	//TODO: Create the setup method that doing the page setup emmidiately after loading (not events)!
	Migur.moodialogs = [];


	/* Create the dnd interaction in "copy" mode. */


	//TODO: BAAAD!!! Ned to insert into module widget
	Migur.dnd.makeAvatar = function(el, droppables, htmlWidget){

		var avatar = el.clone();
		avatar.cloneEvents(el);

		/* Set draggable behaviour to avatar */
		avatar.makeDraggable({

			droppables: droppables,

			onBeforeStart: function(draggable, droppable){

				var coords = draggable.getCoordinates($$('body')[0]);
				var draggableParent = draggable.getParent('div');
				$(draggable).store('source', draggableParent);
			
				// Workaround for unwanted saving of a newsletter when we just drag out a module.
				// Just "lock" the html widget so it will return the same data as it has before dragging
				if (draggableParent) {
					htmlWidget.locked = true;
				}

				$(draggable).setStyle('width', coords.width);
				$$('body').grab(draggable);
				draggable.setStyles({
					left: coords.left + 'px',
					top:  coords.top  + 'px',
					position: 'absolute',
					zIndex: 1000
				});
			},

			onEnter: function(draggable, droppable){
				droppable.addClass('overdropp');
			},

			onCancel: function(draggable, droppable){
				htmlWidget.locked = false;
				return $(draggable).retrieve('dragger').$events.drop[0](draggable, droppable);
			},

			onLeave: function(draggable, droppable){
				droppable.removeClass('overdropp');
			},

			onDrop: function(draggable, droppable){

				htmlWidget.locked = false;

				if (!draggable) return false;

				var source = $(draggable).retrieve('source');
				if (!droppable) {
					// no droopable area

					if (!source) {
						return draggable.destroy();
					}

					source.grab(draggable);
					draggable.setStyles({
						left: 0,
						top:  0,
						position: 'relative',
						zIndex: 1000
					});

				} else {
					// hit in dropable
					var trashcan = $(droppable).hasClass('trashcan');
					if (trashcan) {
						draggable.destroy();

					} else {
						droppable.grab(draggable);
						draggable.setStyles({
							left: 0,
							top:  0,
							position: 'relative',
							zIndex: 'auto'
						});

						draggable.setStyle('width', null);

						var widget = Migur.getWidget(draggable);
						widget.load(
							migurSiteRoot + 'index.php?option=com_newsletter&task=newsletter.rendermodule',
							widget.get(),
							'.widget-content'
							);

						if (widget.get().notConfigured) {
							setTimeout(function(){
								avatar.getElements('a.settings')[0].fireEvent('click');
							}, 500);
						}
					}

					droppable.removeClass('overdropp');

					$('html-area').fireEvent('drop');
				}

				Migur.app.autosaver.update();
			}
		});
		return avatar;
	}

	// BAAAD!!! Ned to insert into module widget
	var avatarSetSettings = function(avatar) {

		avatar.getElements('a.settings')[0]
		.addEvent('mousedown', function(event){
			event.stop();
		})
		.addEvent('click', function(event){

			var widgetEl = $(this).getParent('.widget');
			widget = Migur.getWidget(widgetEl);
			var conf = widget.get().notConfigured;
			widget.set({'notConfigured': false});

			if (event) event.stop();
			var href = 0;
			//$(this).getProperty('href');
//			if ( Migur.moodialogs[href] ) {
//				Migur.moodialogs[href].destroy();
//			}
			Migur.moodialogs[href] = {};
			Migur.moodialogs[href] = new MooDialog.Iframe(
			   '', //$(this).getProperty('href'),
				{
					'title': 'Module / Plugin',
					'class': 'MooDialog myDialog',
					autoOpen: false,
					closeButton: true, //!conf,
					onClose: function() {
						if ( this.task == 'apply' ) {
							var widget = Migur.getWidget(this.targetObj);

							widget.set( {
								'params'    : this.data.params,
								'title'     : this.data.title,
								'showtitle' : this.data.showtitle
							});

							// TODO: The widget should decide it (update or not its content) itself
							if (this.data.type == 1) {
								widget.load(
									migurSiteRoot + 'index.php?option=com_newsletter&task=newsletter.rendermodule',
									widget.get(),
									'.widget-content'
								);
							}
							Migur.app.autosaver.update();
						}
					}
				}
			);
			Migur.moodialogs[href].targetObj = avatar;
			Migur.moodialogs[href].data =
				Migur.getWidget( $(this).getParent('.widget') ).get();

			Migur.moodialogs[href].open();

			var box = document.getSize();
			var scroll = document.getScroll();
			var size = {
				x: $(Migur.moodialogs[href].wrapper).getWidth(),
				y: $(Migur.moodialogs[href].wrapper).getHeight()
			};

			var left = (scroll.x + (box.x - size.x) / 2).toInt();
			var top = (scroll.y + (box.y - size.y) / 2).toInt();


			Migur.moodialogs[href].wrapper.setPosition({
				x: left,
				y: top
			});

			Migur.moodialogs[href].wrapper.grab(new Element('div', {
				'styles': {
					'position': 'absolute',
					'bottom': '0px',
					'right' : '0px',
					'width': '10px',
					'height': '10px'
				},
				'class':'resizer'
			}));
			Migur.moodialogs[href].wrapper.makeResizable({
				handle: Migur.moodialogs[href].wrapper.getElements('.resizer')[0]
			});

			$$('div.title').addEvent('mousedown', function(){
				Migur.moodialogs[href].mover = new Drag.Move(Migur.moodialogs[href].wrapper);
			});

			$$('div.title').addEvent('mouseup', function(){
				Migur.moodialogs[href].mover.detach();
			});


			var ifr = $(Migur.moodialogs[0].content).getElements('iframe')[0];
			var target='moodialogiframe';
			ifr.contentWindow.window.name = target;
			$(ifr).setProperty('name', target);
			$(ifr).setProperty('id', target);

			if ($('moodialog_form_helper')) {
				$('moodialog_form_helper').destroy();
			}

			var phonyForm = new Element('form', {
				'id': 'moodialog_form_helper',
				'method': 'post',
				'action': $(this).getProperty('href'),
				'target': target,
				'styles': {
					'display': 'none'
				}
			});

			phonyForm.enctype = "application/x-www-form-urlencoded"
			$(document.body).grab(phonyForm, 'bottom');

			Object.each(Migur.moodialogs[href].data, function(val, name){

				if (typeof val == 'object') {
					Object.each(val, function(subval, subname){
						phonyForm.appendChild(new Element('input', {
							'type':"hidden",
							'name': 'jform['+name+']['+subname+']',
							'value': subval
						}));
					});
				} else {
					phonyForm.appendChild(new Element('input', {
						'type':"hidden",
						'name': 'jform['+name+']',
						'value': val
					}));
				}	
			});

			phonyForm.submit();
		})
		.setStyle('display', 'block');
	}


	// Create wigets for each template control
	Migur.createWidget('templates-container', {

		data: null,

		render: function() {},
		events: {
			'change': function() {

				var tpl = Migur.iterator.getItem(
					dataStorage.templates,
					't_style_id',
					$(this).get('value')
					);

				if (typeof(tpl.t_style_id) == "undefined") {
					tpl.t_style_id = null;
					tpl.template = "";
				}

				var htmlWidget = Migur.getWidget('html-area');

				var curId = htmlWidget.get().t_style_id;

				if ($(this).get('value') == 0 && curId > 0) {
					alert(Joomla.JText._("YOU_NEED_TO_HAVE_AN_HTML_TEMPLATE", "You need to have an HTML template."));
					$(this).set('value', curId);
					return false;
				}

				if (tpl.t_style_id == curId && curId > 0) {
					alert(Joomla.JText._('THIS_TEMPLATE_STYLE_IS_ACTIVE',"This template style is active."));
					return false;
				}

				if (!curId || confirm(
					Joomla.JText._('DO_YOU_REALLY_WANT_TO_CHANGE_THE_TEMPLATE_STYLE_QM',"Do you really want to change the template style?")+"\n"+
					Joomla.JText._('ALL_CHANGES_IN_THE_CURRENT_HTML_NEWSLETTER_WILL_BE_LOST',"All changes in the current HTML Newsletter will be lost."))) 
				{

					// Case if htmlWidget is rendered firstly
					if ( !curId ) {
						tpl.extensions = dataStorage.htmlTemplate.extensions;
					}

					htmlWidget.render(tpl);

					if (htmlWidget.initialised) {
						Migur.app.autosaver.update();
					}

					// change template
					// set new droppables for drags
					$$('.html-slider-modules .drag').each(function(el){
						var dragger = el.retrieve('dragger');
						if (dragger) {
							dragger.droppables = $$('#html-area .modules');
						}
					});
				}
				return false;
			}
		}
	});


	/* Create MODULE widgets */
	$$('.html-slider-modules .drag').each(function(el) {

		var dataModule = Migur.iterator.getItem(
			dataStorage.modules,
			'extension',
			el.getProperty('id'));

		Migur.createWidget(el, {

			data: dataModule,

			type: dataModule.extension,

			mode: 'common',

			events: {
				/* Initialize DND */
				'mousedown': function(event){

					event.stop();

					var widget = Migur.getWidget(this);

					if (widget.mode == 'common') {

						/* If event happened onto setting icon */

						// IE Fix
						var trgt = (typeof(event.event.target) == 'undefined')?
						event.target : event.event.target;

						if ( $(trgt).hasClass('settings') ) {
							event.stop();
							return;
						}


						var avatar = Migur.dnd.makeAvatar(this, $$('#html-area .modules, #trashcan-container'), widgetHtmlArea);

						var w = Migur.createWidget(
							avatar,
							{
								data: [widget.get()].clone()[0],
								type: widget.type,
								mode: 'custom'
							}
							);

						w.set({'notConfigured': true});

						avatarSetSettings(avatar);
						avatar.inject($$('body')[0]);

						// positionnig the Avatar in the same place where element is placed
						var coords = this.getCoordinates($$('body')[0]);
						avatar.setStyles({
							left: coords.left-5 + 'px',
							top:  coords.top-5  + 'px',
							position: 'absolute',
							zIndex: 1000
						});

						// Init dragging of an AVATAR
						avatar.fireEvent('mouseover', event);
						avatar.fireEvent('mousedown', event);

					} else {
						/* If event happened onto setting icon */
						// IE Fix
						var trgt = (typeof(event.event.target) == 'undefined')?
						event.target : event.event.target;
						if ( $(trgt).hasClass('settings') ) {
					}
					}
				}
			}
		});

		$(el).getElements('.settings')[0]
		.addEvent('mousedown', function(event){
			event.stop();
		})
		.addEvent('click', function(event){
			event.stop();
		});

	});


	/* Create PLUGIN widgets */
	$$('.plugin').each(function(el) {

		Migur.createWidget(el, {

			setup: function(){
				var id = $(this.domEl).getProperty('id');
				var data = Migur.iterator.getItem(
					dataStorage.htmlTemplate.extensions,
					'extension',
					id
				);

				// If this widget is not used yet then set default
				if (data.length == 0) {
					data = Migur.iterator.getItem(
						dataStorage.plugins,
						'extension',
						id
					);
				}

				this.set(data);
				// Update the turn on/off switcher
				$(this.domEl).getElements('input')[0].setProperty(
					'checked',
					(data.params !== undefined && data.params.active)? true : false
				);
			},

			/* Uses parent getter and setter*/

			events: {
				'click': function(event){
					/* If event happened onto checkbox */
					// IE Fix
					var trgt = (typeof(event.event.target) == 'undefined')?
						event.target : event.event.target;

					if ( $(trgt).match('input') ) {
						var widget = Migur.getWidget(this);
						var data = widget.get();
						data.params.active = $(trgt).getProperty('checked');
						widget.set(data);

						Migur.app.autosaver.update();
					}
				}
			}
		});
		avatarSetSettings(el);
	});


	// Create widget for HTMLarea contol
 	var widgetHtmlArea = Migur.createWidget($('html-area'), {

	/*
	 * Parses input data from data storage and get
	 * the structure of template style and add all the extensions
	 * used in the newsleter html templte
	 */
		setup: function(){
			var htmlTemplate = dataStorage.htmlTemplate;
			if ($(htmlTemplate.template.id)) {
				this.data =
				[Migur.getWidget($(htmlTemplate.template.id)).get()].clone()[0]
			} else {
				this.data = {};
			}
			this.data.extensions = htmlTemplate.extensions;
		},

		/* Update the dom of element and save the id of used tempate style */
		render: function(data) {

			if (!data || !data.t_style_id) {
				return false;
			}

			this.data = data;

			this.domEl.set('html', '');

			var $this = this;
			new Request.JSON({
				url: '?option=com_newsletter&task=template.getparsed&format=html',
				data: {
					t_style_id: data.t_style_id,
					type: 'html',
					tagsRenderMode: 'htmlconstructor'
				},
				onSuccess: function(res){

					$($this.domEl).removeClass('preloader');

					$this.data.template = res.data.content;
					this.initialised = false;
					
					$this._render();

					Migur.app.autosaver.update();
				}
			}).send();

			this.domEl.addClass('preloader');

			return true;
		},

		/* Uses the this.data[t_style_id, template] */
		_render: function() {
			if (typeof (this.data.template) == 'string') {

				var domEl = this.domEl;

				/* Render the template HTML */
				domEl.setStyle('border', (this.data.template == "")? '1px solid #ff0000' : '0');
				domEl.set({
					html: this.data.template
					});
				$$('form [name=jform[t_style_id]]').set('value', this.data.t_style_id);

				var locThis = this;

				// If nothing to render
				if (!this.data.extensions) {
					locThis.initialised = true;
					return;
				}

				// Render the modules HTML

				// Count how many modules should be rendered
				var modules = [];
				Array.each(widgetHtmlArea.data.extensions, function(el) {

					// Check if there is a position for module
					var position = $$(
						'#' + domEl.getProperty('id') +
						' [name=' + el.position + ']'
						)[0];
					if (position) { 
						modules.push(el); 
					}
				});

				// Finish it if there is no modules to render
				if (modules.length == 0) {
					locThis.initialised = true;
					return;
				}

				// Process each module
				Array.each(modules, function(el, idx) {

					// Create the new Element
					var module = $$('#' + el.extension)[0];
					if (module) {

						var avatar = Migur.dnd.makeAvatar(module, $$('#html-area .modules, #trashcan-container'), widgetHtmlArea);
						// Create the new Widget from element
						var widget = Migur.getWidget(module);
						var newW = Migur.createWidget(
							avatar,
							{
								data: el,
								type: widget.type
							}
							);

						newW.load(
							migurSiteRoot + 'index.php?option=com_newsletter&task=newsletter.rendermodule',
							newW.get(),
							'.widget-content',
							// Callback that checks if this module is the last module in list
							function(){
								if(modules.length == idx + 1){
									widgetHtmlArea.initialised = true;
								}
							}
						);

						avatarSetSettings(avatar);

						var position = $$(
							'#' + domEl.getProperty('id') +
							' [name=' + el.position + ']'
						)[0];

						avatar.inject(position);
					}
				});
			}
			
			locThis.initialised = true;
		},

		/**
		* Specific behavior. Parses dom of htmlTpl element, get all the extensions
		* its positions and id of used template style and
		* return this data in JSON format
		**/
		parse: function() {
			
			// Workaround for unwanted saving of a newsletter when we just drag out a module.
			// Just "lock" the html widget so it will return the same data as it has before dragging
			if (widgetHtmlArea.locked == true) {
				return widgetHtmlArea.dataCache;
			}
			
			// all of dropable areas
			var dt = [this.data].clone()[0];
			if (!dt) dt = {};
			dt.template = null;
			dt.extensions = null;
			var res = {
				template: dt,
				extensions: []
			};

			var idx = 1;
			$$('#' + this.domEl.getProperty('id') + ' .modules').each(
				function(droppable){
					droppable.getChildren('.drag').each(function(draggable){
						var module = Migur.getWidget(draggable);
						var data = [module.get()].clone()[0];
						data.position = droppable.get('name');
						data.ordering = idx;
						res.extensions.push(data);
						idx++;
					});
				}
			);

			widgetHtmlArea.dataCache = res;
			return res;
		}
	});

})