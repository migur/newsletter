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
	Migur.dnd.makeAvatar = function(el, droppables){

		var avatar = el.clone();
		avatar.cloneEvents(el);

		/* Set draggable behaviour to avatar */
		avatar.makeDraggable({

			droppables: droppables,

			onBeforeStart: function(draggable, droppable){


				var coords = draggable.getCoordinates($$('body')[0]);
				$(draggable).store('source', draggable.getParent('div'));
				$(draggable).store('source', draggable.getParent('div'));
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
				return $(draggable).retrieve('dragger').$events.drop[0](draggable, droppable);
			},

			onLeave: function(draggable, droppable){
				droppable.removeClass('overdropp');
			},

			onDrop: function(draggable, droppable){

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
