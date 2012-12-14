Migur.define('importer', function(){

	var self = this;
	
	this.filename = null;

    this.getHead = function(file) {

		if (!file) {
			alert(Joomla.JText._('UPLOAD_FILE_FIRST', 'Please upload file lirst'));
			return;
		}

        var settings = self.getSettings() || {};
		settings.file = file;
		
        var id = $$('[name=list_id]')[0].get('value');
        new Request.JSON({
            url: '?option=com_newsletter&task=list.gethead',
            onComplete: self.headParser
        }).send( '&list_id=' + id + '&jsondata=' + JSON.encode(settings) );
    }


    this.headParser = function(req) {

		var parser = new Migur.jsonResponseParser();
		parser.setResponse(req);

		var data = parser.getData();

		if (parser.isError()) {
			alert(
				parser.getMessagesAsList(Joomla.JText._('AN_UNKNOWN_ERROR_OCCURED', 'An unknown error occured!'))
			);
			return;	
		}
		
         if(!data || !data.fields || data.fields.length == 0) {
            alert(Joomla.JText._('NO_FIELDS_FOUND', 'No fields found!'));
            return;
        }

		$('import-file').removeClass('hide');
		$$('#import-file .drag').destroy();

		for(var i=0; i < data.fields.length; i++) {
			var newEl = new Element(
					'div',
					{
						'html'    : data.fields[i],
						'class'   : 'drag',
						'position': 'relative',
						'rel'     : i
					}
				)

			$('import-found-fields').grab(newEl);
			Migur.dnd.makeDND(newEl, $$('#import-file .drop'));
			newEl.setStyle('position', 'relative');
		}

//            $('exclude-file').removeClass('hide');
//            ctr.getElements('.drag').destroy();
//
//            for(var i=0; i < data.fields.length; i++) {
//                var newEl = new Element(
//                        'div',
//                        {
//                            'html'    : data.fields[i],
//                            'class'   : 'drag',
//                            'position': 'relative',
//                            'rel'     : i
//                        }
//                    )
//
//                $('exclude-found-fields').grab(newEl);
//                Migur.dnd.makeDND(newEl, $$('#exclude-file .drop'));
//                newEl.setStyle('position', 'relative');
//            }
    }


    this.getSettings = function() {

        var res = {
            fields: {},
            delimiter: null,
            enclosure: null
        };

//        if (type == 'import') {
            if ( !$('import-delimiter').hasClass('hide') ) {
                res.delimiter = $('import-delimiter').get('value');
            } else {
                res.delimiter = $('import-delimiter-custom').get('value');
            }

            if ( !$('import-enclosure').hasClass('hide') ) {
                res.enclosure = $('import-enclosure').get('value');
            } else {
                res.enclosure = $('import-enclosure-custom').get('value');
            }

            res.overwrite = $('import-overwrite').getProperty('checked');
            res.skipHeader = $('import-skip-header').getProperty('checked');
//        }
//
//        if (type == 'exclude') {
//
//            if ( !$('exclude-delimiter').hasClass('hide') ) {
//                res.delimiter = $('exclude-delimiter').get('value');
//            } else {
//                res.delimiter = $('exclude-delimiter-custom').get('value');
//            }
//
//            if ( !$('exclude-enclosure').hasClass('hide') ) {
//                res.enclosure = $('exclude-enclosure').get('value');
//            } else {
//                res.enclosure = $('exclude-enclosure-custom').get('value');
//            }
//			
//            res.skipHeader = $('exclude-skip-header').getProperty('checked');
//        }

        if (!res.delimiter) {
            alert(Joomla.JText._('THE_DELIMITER_IS_NOT_SET','The delimiter is not set'));
            return false;
        }

        return res;
    }
	
	this.onUpload = function(data) {

		if (data.status == 1) {
			
			self.filename = data.file.filepath;
			self.getHead(data.file.filepath);
			
		} else {
			
			self.filename = null;
		}
	}
	
	this.importData = function() {

        var res = self.getSettings();

        var notEnough = false;

        $$('#import-fields .drop').each(function(el){

            var field = el.getProperty('rel');
            var drag = el.getChildren('.drag')[0];
            var def = null;
            var mapped = null;

            if (field == 'html') {
                def = $('import-file-html-default').get('value');
            }

            if (!drag) {
                if (field != 'html') {
                    notEnough = true;
                }
            } else {
               mapped = drag.getProperty('rel');
            }

            res.fields[field] = {
                'mapped' : mapped,
                'default': def
            };

        });

        if (notEnough == true) {
            alert(Joomla.JText._('PLEASE_FILL_ALL_REQUIRED_FIELDS','Please fill all required fields'));
        } else {

            $$('[name=subtask]').set('value', 'import-file-apply');
            var id = $$('[name=list_id]')[0].get('value');

			var importMan = new Migur.iterativeAjax({
				
                url: '?option=com_newsletter&task=list.import',
				
				data: {
					jsondata: JSON.encode(res),
					list_id: id
				},
				
				limit: 1000,
				
				messagePath: '#import-file #import-message',
				preloaderPath: '#import-file #import-preloader',
				
                onComplete: function(messages, data){
					
                    this.showAlert(
					
						messages,
						
						Joomla.JText._('TOTAL','Total')+": " + data.total + "\n"+
						Joomla.JText._('SKIPPED','Skipped')+": " + data.skipped + "\n"+
						Joomla.JText._('ERRORS', 'Errors')+": " + data.errors + "\n"+
						Joomla.JText._('ADDED', 'Added')+": " + data.added + "\n"+
						Joomla.JText._('UPDATED', 'Updated')+": " + data.updated + "\n"+
						Joomla.JText._('ASSIGNED', 'Assigned')+": " + data.assigned + "\n"
					);
						
                    document.location.reload();
                }
            });
				
			importMan.start();
        }
    }		

	this.init = function() {
		
		window.MigurImportUploadCallback = this.onUpload;
		
		$('import-file-refresh').addEvent('click', function(){
			self.getHead(self.filename);
	    });
		
		$('import-file-apply').addEvent('click', function(){
			self.importData();
		});
	}

	this.init();
	
});


