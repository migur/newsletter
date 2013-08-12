/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// Only define the Joomla namespace if not defined.
if (typeof(Migur) === 'undefined') {
    var Migur = {};
}


/**
 * Common class for all ajax actions.
 *
 * @package Migur.newsletter
 * @since   1.0
 *
 */
Migur.ajax = {};

/**
 * The autosaver object. Implement the repeated ajax saving on basis
 * of state of observed data.
 *
 * @since  1.0
 */
Migur.ajax.autosaver = new Class({

    options: {

        /* If set then use repeat sending, and non-repeat otherwise */
        repeat: true,

        /**
         * The time interval between AJAX sends measured in ms,
         * uses only if flag "repeat" is true
         **/
        timeout: 30000,

        /**
         * The form (or a DOM element) who's state will be checked.
         **/
        observable: null,

        /**
         * The state of the observable DOM element
         **/
        observState: null
    },

    initialize: function(config) {

        /* Populate THIS with overrided methods */
        config.options = Object.merge(this.options, config.options);
        Object.append(this, config);
        this._setupAjax();
    },

    start: function(options) {

        /* Set the repeat option */
        if (options) {
            this.options = Object.merge(this.options, options);
        }

        /* If repeated saving is not in process then let's start it! */
        if (!this._intervalId) {

            var obj = this;

            /**
             * Send emmidiately.
             * The _send calls only if this.controller() returns true
             **/
            obj.send(options, 'useController');

            /* Set the repeater */
            this._intervalId = setInterval(
                function(){
                    obj.send(options, 'useController');
                },
                obj.options.timeout
                );
        }

    /**
         *  If user wants to start a repeated saving
         *  and repeated saving is already in process
         *  then nothing to do
         **/
    },

    stop: function() {
        /* Determine if it's need to cancel the repeat requests */
        if (this._intervalId) {
            clearInterval(this._intervalId);
            this._intervalId = null;
        }
    },

    beforeSend: function(){},

    /* Sand data only once
     * @param useController - if you want use the controller method before sending
     * @param options       - options and/or data to be passed to _send
     * @param additional    - data to append to data for _send
     **/
    send: function (options, useController, additional) {

        this.beforeSend();
        
        if (!options) options = {};
		
		if (!options.data) {
			options.data = this.getter();
		}
		
        if ( !useController || this.controller(options.data) ) {

            if (typeof options.data == 'string' &&
                typeof additional   == 'string') {
                options.data = options.data + additional;
            }

            if (typeof options.data == 'object' &&
                typeof additional   == 'object') {
                Object.append(options.data, additional);
            }

            return this._send(options);
        }

        return true;
    },


    /**
     * Override this function to add behaviour.
     * If return true, then _send is called from start.
     * If return false, then _send isn't called from start
     **/
    controller: function(data){
        return this.isChanged(data);
    },

    /**
     * Allow to add behavior how to get data for saving.
     **/
    getter: function(){
        return this.options.observable;
    },

    isChanged: function(data) {
		
		// First access
		if (this.options.observState === null) {
			this.options.observState = data;
			return false;
		}
		
        var res = ( this.options.observState != data  || this.forceIsChanged);
		this.forceIsChanged = false;
		return res;
    },

    /* Need to update state!!!! */
    onSuccess: function(){},
    onError:   function(){},

    _setupAjax: function() {
        // default event handlers

        if ( this.options && typeof this.options.onSuccess != 'function' ) {
            this.options.onSuccess = this.onSuccess;
        }

        if ( this.options && typeof this.options.onError != 'function' ) {
            this.options.onError = this.onError;
        }

        // create object
        if (typeof(this.options.ajax) != 'object') {
            this.options.ajax = new Request.JSON(this.options);
        }
    },


    /* Implements data sending */
    _send: function (options) {
       return this.options.ajax.send(options);
    }
});
