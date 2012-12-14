/**
 * The javascript file for smtpprofile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function(options) {
		
		var self = this;
		
		this.form = null;
		
		this.preloader = null;
		
		// onSubmit event handler. 
		// Modify it to change behavior
		this.onSubmit = function() {
			self._showPreloader();
		}
		
		// Show preloader method
		this._showPreloader = function() {
			if (self.preloader) {
				$(self.preloader).addClass('preloader');
			}	
		}
		
		// Hide preloader method
		this._hidePreloader = function() {
			if (self.preloader) {
				$(self.preloader).removeClass('preloader');
			}	
		}
		
		// Init method. Passes object of options.
		this.init = function(options) {
			
			if (!options) options = {};
			
			self.form = options.form? 
				options.form : $$('[name="uploadForm"]')[0];

			self.preloader = options.preloaderDomEl? 
				options.preloaderDomEl : $(self.form).getElements('.preloader-container')[0];
			
			self.form.addEvent('submit', self.onSubmit);
		}
		
		this.init(options);
	}
);
