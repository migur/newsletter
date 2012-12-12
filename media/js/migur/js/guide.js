
/*
 * Steps = [ .....
 *
 *  {
 *      target: {
 *        dom:   'css-selector',
 *        event: 'event-name-of-a-target'
 *      },
 *      needle: {
 *        dom:   'css-selector',
 *      },
 *      overlay: {
 *        dom:   'HTML-code-for-this-current-step'
 *      },
 *      passed: true/false
 *  }
 *   *  ....... ]
 */


Migur.widgets.guide = new Class({

	Implements: Migur.widget,

	coords: [],

    initialize: function(element, config){

        this.steps = config.steps;
        this.current = 0;

		if (!element) {
			
			element = new Element('div', {
				html:   step.overlay.dom,
				styles: {
					left: coords.left + 'px',
					top:  coords.top  + 'px',
					position: 'absolute',
					zIndex: 1000
				}
			});
		}
		$$('body')[0].grab(element);
		this.domEl = element;

        this.domEl.set({
            'html'  : (config.body)? config.body : '<div class="guide-content"></div>',
            'styles': {
                position: 'absolute',
                zIndex: 1000
			}

        });

        if (config.stopControl) {
            this.stopControl();
        }
    },

    setContent: function(body){
        this.domEl.getElements('.guide-content')[0].set({
            html: body
        });
    },

    start: function(){
        this.destroyStep();
        this.createStep(0);
    },

    stop: function(){
        this.destroyStep();
    },

    /**
     * Go to the next step
     */
    prev: function(){
        var step = this.currentStep;
        this.destroyStep();
        this.createStep(step-1);
    },

    /**
     * Back to the previous step
     */
    next: function(){
        var step = this.currentStep;
        this.destroyStep();
        this.createStep(step+1);
    },

    createStep: function(stepNo){

		if (!this.steps[stepNo]) {
			return false;
		}

        var step = this.steps[stepNo];

        // Create overlay
        if (step.overlay.body) {
            this.setBody(step.overlay.body);
        }
        if (step.overlay.content) {
            this.setContent(step.overlay.content);
        }
		

        // Bind the event
		if (step.target && step.target.dom) {

			var dom = this._getDom(step.target.dom);

			var widget = this;
			this.currentListener = function() {
				widget.stepListener.apply(widget, arguments);
			};
			dom.addEvent(step.target.event, this.currentListener);
		}	

		if (step.needle && step.needle.dom) {

			// Get the position of the needle
			var coords = this._getDom(step.needle.dom).getCoordinates($$('body')[0]);
			coords.h = this.domEl.getHeight();
			coords.w = this.domEl.getWidth();

			var xCor = step.needle.xCorrection? step.needle.xCorrection : 0;
			var yCor = step.needle.yCorrection? step.needle.yCorrection : 0;

			coords.top  = coords.top  - coords.h + yCor;
			coords.left = coords.left + xCor;

			var wdgt = this;

			if (stepNo < this.steps.length - 1){

				new Fx.Morph(this.domEl, {
					duration: 1000
				}).start({
					left: coords.left,
					top:  coords.top
				});

				// Go baby, go, go! But after MORPH!
				setTimeout(function(){
					wdgt.bounceIt();
				}, '1000');

			} else {

				// Go baby, go, go! But after MORPH!
				setTimeout(function(){
					
					new Fx.Morph(wdgt.domEl, {
						duration: 3000,
						transition: Fx.Transitions.Sine.easeOut
					}).start({
						left: -500,
						top: -500
						
					});
				}, '2000');

				setTimeout(function(){
					wdgt.domEl.destroy();
					delete(wdgt);
				}, '5000');
			}
		}	
		
		this.currentStep = stepNo;

    },

    destroyStep: function(){

		if (typeof (this.currentStep) != 'number' ||
			typeof (this.steps[this.currentStep]) != 'object') {
		
			return false;
		}	

		var step = this.steps[this.currentStep];
		
		var dom = this._getDom(step.target.dom);
        dom.removeEvent(step.target.event, this.currentListener);
		dom.eliminate('migur-widget-guide');

        this._fx.cancel();
        delete (this._fx);
        
        this.currentStep = null;
		
		return true;
    },
	
	_getDom: function(selector) {
	
		if (typeof(selector) == 'function') {
			return selector();
		}

		return $$(selector)[0];
	},
	
	bounceIt: function(){

		this.coords = this.domEl.getCoordinates($$('body')[0]);
		var wdgt = this;

		if (!this._fx) {
			this._fx = new Fx.Morph(this.domEl, {
				duration: 400, 
				transition: Fx.Transitions.Sine.easeOut,
				link: 'chain',
				onChainComplete: function() { 
					this
						.start({top: wdgt.coords.top + 10 })
						.start({top: wdgt.coords.top + 5});
				 }
			});
		}
		
		this._fx.start();
	},


    /*
     * It is called when the event is fired
     * this - the widget object
     */
    stepListener: function(){
        
        this.next();
    },

    /*
     * Handles the stop button
     */
    stopControl: function(){
		var ctrl = $(this.domEl).getElements('.guide-stop')[0];
        if (ctrl) {
            ctrl.store('migur-widget-guide', this);
            var widget = this;
            ctrl.addEvent('click', function() {
                widget.stopListener.apply(widget, arguments);
            });
        }
    },

    stopListener: function(){
        this.stop();
        this.domEl.destroy();
        delete(this);

        // All died...
    }
});