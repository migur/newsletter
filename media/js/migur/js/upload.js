/**
 * @modified Andrey
 */

Uploader = new FancyUpload2($('upload-flash'), $('upload-queue'), {
    verbose: true,
    url: $('listForm').action,
    path: '/joomla/media/system/swf/uploader.swf',
    multiple: false,
    target: $('upload-browse'),
    instantStart: false,
    allowDuplicates: true,
    fileSizeMax: 10485760,
    fileListMax: 1,
    typeFilter: {
        'Files (*.bmp, *.csv, *.doc, *.gif, *.ico, *.jpg, *.jpeg, *.odg, *.odp, *.ods, *.odt, *.pdf, *.png, *.ppt, *.swf, *.txt, *.xcf, *.xls, *.BMP, *.CSV, *.DOC, *.GIF, *.ICO, *.JPG, *.JPEG, *.ODG, *.ODP, *.ODS, *.ODT, *.PDF, *.PNG, *.PPT, *.SWF, *.TXT, *.XCF, *.XLS)': '*.bmp; *.csv; *.doc; *.gif; *.ico; *.jpg; *.jpeg; *.odg; *.odp; *.ods; *.odt; *.pdf; *.png; *.ppt; *.swf; *.txt; *.xcf; *.xls; *.BMP; *.CSV; *.DOC; *.GIF; *.ICO; *.JPG; *.JPEG; *.ODG; *.ODP; *.ODS; *.ODT; *.PDF; *.PNG; *.PPT; *.SWF; *.TXT; *.XCF; *.XLS'
    },
    onBeforeStart: function(){
        Uploader.setOptions({
            url: $('listForm').action + '&task=list.upload&folder=data&format=json'
        });
    },


    onSelect: function() {
        this.status.removeClass('status-browsing');
        Uploader.start();
    },

    onFileSuccess: function(file, response) {

        var json = new Hash(JSON.decode(response, true) || {});
        if (json.get('status') == '1') {
            file.element.addClass('file-success');
            $$('.file-name').set('html', '<strong>' + file.name + '</strong>');
            file.info.set('html', '<strong>' + Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_SUCCESSFULLY_UPLOADED', 'Ok') + '</strong>');

            if (typeof Uploader.afterComplete == 'function') {
                Uploader.afterComplete(file, response);
            }

        } else {
            file.element.addClass('file-failed');
            file.info.set('html', '<strong>' + json.get('error') + '</strong>');
        }
    },
    onLoad: function() {
        document.id('upload-flash').removeClass('hide'); // we show the actual UI
        document.id('upload-noflash').destroy(); // ... and hide the plain form

        // We relay the interactions with the overlayed flash to the link
        this.target.addEvents({
            click: function() {
                return false;
            },
            mouseenter: function() {
                this.addClass('hover');
            },
            mouseleave: function() {
                this.removeClass('hover');
                this.blur();
            },
            mousedown: function() {
                this.focus();
            }
        });
    },

    onBrowse: function(){
        Uploader.remove();
    }
});
