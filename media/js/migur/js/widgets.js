/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */


Migur.widgets = {};

/**
 * Common class for all widgets. Implements the standard functionality
 * for all widgets.
 *
 * @package Migur.newsletter
 * @since   1.0
 */
Migur.widget = new Class({

    Extends: Events,

    data: {},

    initialize: function(element, config){
        if (!element) {
            return false;
        }
        this.domEl = $(element);

        if (!config) {
            config = {};
        }
        else {
            Object.append(this, config);
        }

        /* Bind events */
        if ( config.events ) {
            Object.each(config.events, function(func, key){
                $(element).addEvent(key, func);
            });
        }
        /* If you need the specific setup just override it */
        $(element).addClass('widget');
        this.setup(this.data);
        this.render();
    },

    /* Calls when widget is created */
    setup: function() {
        return;
    },

    /* Sets the data of widget. Only objects are supplied */
    set: function(data) {
        if (typeof data == 'object') {
            Object.append(this.data, data);
            return true;
        }
        return false;
    },

    /* Sets the HTML body of widget. Only string are supplied */
    setBody: function(html, subpath){
        if (!html) {
            html = '';
        }

        var el = (subpath)? this.domEl.getElement(subpath) : this.domEl;
        if (el != null) {
            el.set({
                'html': html
            });
            return true;
        }
        
        return false;
    },

    /* Gets the data of widget */
    get: function() {
        return this.data;
    },

    /* Gets the HTML body of widget */
    getBody: function(){
        return this.domEl.get('html');
    },

    load: function(url, data, subpath){

        var widget = this;

        var transport = new Request({
            url: url,
            data: data,

            onComplete: function(res){
                widget.setBody(res, subpath);
                delete transport;
            }
        }).send();
    },

    /* Allow alternative more deeply getting the data of widget */
    parse: function() {
        return $(this.domEl).get('html');
    },

    /* Method to render the widget view */
    render: function(data) {
        if (data) {
            this.data = data;
        }
        if (this.data && this.data.template) {
            this.domEl.set({
                html: this.data.template
                });
        }
    },

    /* Method to update the widget view */
    update: function() {
        $(this.domEl).set({
            value: data
        });
    },

    getType: function() {
        return this.type;
    }
});


/**
 * Create widget for DOM element
 * 
 * @param el  - the DOM element of widget
 * @param obj - the config of widget
 * @param obj - optionaly the class of a widget
 * 
 * @return boolean
 * @since  1.0
 */
Migur.createWidget = function(el, obj, wdgt) {
    if (!$(el)) {
        console.log('Migur.createWidget("' + el + '", obj). El is not a DOM element');
        return false;
    }

    if (typeof obj != 'object') {
        console.log('Migur.createWidget(el, obj). Obj is not an object');
        return false;
    }

	var widget = null;
	if (!wdgt) {
	    widget = new Migur.widget(el, obj);
	} else {
		widget = new wdgt(el, obj);
	}		
    $(el).store(
        'widget',
        widget
        );

    Migur.widgetStorage.set(widget);
    
    return widget;
}

/**
 * Retrieve the widget of a DOM element
 *
 * @param el - the DOM element of widget
 *
 * @return mixed - object or false
 * @since  1.0
 */
Migur.getWidget = function(el) {
    if ($(el)) {
        return $(el).retrieve('widget');
    } else {
        console.log('Migur.getWidget("' + el + '"). El is not a DOM element');
    }
        
    return false;
}
    


Migur.widgetStorage = {

    _storage: [],

    /**
     * Add widget to storage. Set for it the internal unique id
     *
     * @param  widget - the widget object
     * @return null on fail, widget id on success
     */
    set: function(widget){

        if (typeof(widget.widgetId) == 'undefined') {
            widget.widgetId = this._storage.length + 1;
        }
        
        if (typeof (this._storage[widget.widgetId]) != 'undefined') {
            console.log('Conflict in the widget storage. Widget id ' + widget.widgetId);
            return null;
        } else {
            this._storage[widget.widgetId] = widget;
        }
        return widget.widgetId;
    },

    /**
     * Get link to the widget from storage by its unique id.
     *
     * @param  widgetId - the widget id
     * @return null on fail, widget object on success
     */
    get: function(widgetId){
        if (typeof (this._storage[widgetId]) == 'undefined') {
            return null;
        } else {
            return this._storage[widgetId];
        }
    }
}
