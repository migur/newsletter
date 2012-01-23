/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */


/**
 * The main object of Migur library
 *
 * @since 1.0
 */
if (typeof(Migur) === 'undefined') {
    var Migur = {};
}


/**
 * The object for handling lists actions.
 */
Migur.lists = {};

/**
 * The functionality for iterable objects.
 */
Migur.iterator = {};

/**
 * The drag'n'drop extended functionality.
 */
Migur.dnd = {};

/**
 * The charts extended functionality.
 */
Migur.chart = {};

/**
 * The place for specific data used on the page.
 */
Migur.storage = {};

/**
 * Translations.
 */
Migur.translations = {};

/**
 * Namespace for all methods and objects for current page (application)
 */
Migur.app = {};


/**
 * Need for Table Column ordering in multiple-forms page.
 * Expand the functionality to handle multiform pages
 *
 * @param order  - the name of column to order
 * @param dir    - the direction of column to order
 * @param task   - the action to execute
 * @param formId - the NAME of submitted form
 *
 * @return boolean
 * @since  1.0
 */
Migur.lists.tableOrdering = function(order, dir, task, formId)
{
    if (typeof(formId) === 'undefined') {
        return tableOrdering(order, dir, task);
    }
    var form = document[formId];

    if (typeof(form.filter_order) != 'undefined') {
        form.filter_order.value = order;
    }

    if (typeof(form.filter_order_Dir) != 'undefined') {
        form.filter_order_Dir.value = dir;
    }

    return Joomla.submitform(task, form);
}

/**
 * Expand the functionality to handle multiform pages.
 * Handles the amount of checked checkboxes.
 *
 * @param value  - the value of checkbox
 * @param formId - the NAME of submitted form
 *
 * @return void
 * @since  1.0
 */
Migur.lists.isChecked = function(value, formId)
{
    if (value == true) {
        document.forms[formId].boxchecked.value++;
    } else {
        document.forms[formId].boxchecked.value--;
    }
}


/**
 * Performs the action for thecked rows
 *
 * @param id     - the id of clicked element
 * @param task   - the action to execute
 * @param formId - the NAME of submitted form
 *
 * @return boolean
 * @since  1.0
 */
Migur.lists.listItemTask = function (id, task, formId)
{
    if (typeof(formId) === 'undefined') {
        return listItemTask(id, task);
    }
    var f = document[formId];
    var cb = f[id];
    if (cb) {
        for (var i = 0; true; i++) {
            var cbx = f['cb'+i];
            if (!cbx)
                break;
            cbx.checked = false;
        } // for
        cb.checked = true;
        f.boxchecked.value = 1;
        Joomla.submitform(task, f);
    }
    return false;
}


/**
 * Adds the sortable functionality to HTML table.
 * Can be used with Migur.lists.paginator.
 *
 * @since  1.0
 */
Migur.lists.sortable = {

    //TODO: Move to widgets.

    setup: function(table) {
        // parseHead
        obj = this;
        $(table).getChildren('thead a').each(function(el){
            var imgEl = el.getChildren('img')[0]
            if (!imgEl) {
                imgEl = new Element('img');
                $(el).grab(imgEl);
                imgEl.setStyle('display', 'none');
            } else {
                // invert
                var dir = (imgEl.getProperty('src')
                    .indexOf('asc') > -1)? 'asc':'desc';
                obj._setDirection(el, dir);
            }
        });

        $(table).getElements('thead a').each( function(el) {
            el.removeEvents('click')
            .setProperty('onclick', false)
            .addEvent('click', function(){

                var clicked = this;
                var colNum = -1;
                var tHead = $(clicked).getParent('thead');
                var domContainer = $(tHead).getSiblings('tbody')[0];

                tHead.getElements('th').each(function(elem, idx, elements){
                    var el = elem.getElements('a')[0];
                    if (el && el == clicked) {
                        colNum = idx;
                    }
                });

                if(colNum >= 0) {
                    // get Direction (by Icon png or class)
                    dir = Migur.lists.sortable.changeDirection(clicked);
                    Migur.lists.sortable.sort(domContainer, clicked, colNum, dir);
                //changeIcon
                }

                return false;
            });
        });
    },


    sort: function(domContainer, clicked, colNum, dir){

        var rows = domContainer.getElements('tr');
        var items = [];
        rows.each(function(el){
            var tds = el.getChildren('td');
            items.push({
                value: tds[colNum].get('text').trim(),
                domEl: el
            })
        });

        for(var i=0; i < items.length; i++) {
            for(var j=i; j < items.length; j++) {

                if ( (dir == 'asc'  && items[i].value > items[j].value) ||
                    (dir == 'desc' && items[i].value < items[j].value) ) {

                    var buff = items[i];
                    items[i] = items[j];
                    items[j] = buff
                    delete buff;
                }
            }
        }

        for(var i=0; i < items.length; i++) {
            domContainer.grab(items[i].domEl);
        }

        this.afterSort(domContainer, clicked, dir);
    },

    setDirection: function(clicked, dir){
        return this._setDirection(clicked, dir);
    },

    _setDirection: function(clicked, dir){
        $(clicked).getParent('thead').getElements('img')
        .setStyle('display', 'none');

        $(clicked).getParent('thead').getElements('a')
        .removeClass('direction-asc')
        .removeClass('direction-desc');


        $(clicked)
        .addClass('direction-' + dir)
        .getChildren('img')
        .setProperty('src', '../media/system/images/sort_' + dir + '.png')
        .setStyle('display','inline');

        this._setIcon(clicked, dir);
    },

    _getDirection: function(clicked){
        return ($(clicked).hasClass('direction-asc'))? 'asc':'desc';
    },
    changeDirection: function(clicked){
        var dir = (this._getDirection(clicked) == 'asc')? 'desc':'asc';
        this._setDirection(clicked, dir);
        return dir;
    },

    _setIcon: function(clicked, dir){
        // setIcon call
        $(clicked).getChildren('img')[0]
        .setProperty('src', '../media/system/images/sort_' + dir + '.png');
    },

    afterSort: function(domContainer, clicked, dir){
        //Add odd classes
        domContainer.getChildren().each(function(tr, idx){
            $(tr).removeClass('row0');
            $(tr).removeClass('row1');
            $(tr).addClass('row' + (idx % 2));
        });


        var pagination = domContainer.getParent().getElements('.pagination')[0];
        if (pagination) {
            var paginator = pagination.retrieve('paginator');
            if ( paginator ) {
                paginator.refresh();
            }
        }
    }
};

/**
 * Adds the pagination functionality to HTML table.
 * Can be used with Migur.lists.sortable
 *
 * @since  1.0
 */
Migur.lists.paginator = new Class({

    //TODO: Move to widgets.

    initialize: function(options){
        this.setup(options);
    },

    setup: function(table) {

        this.doms = {};
        this.storage = $(table).getElements('tbody')[0]
        this.container = $(table).getElements('.pagination')[0];
        this.pages = this.container.getElements('.page')[0];
        this.doms.start = $(this.container).getElements('.start')[0];
        this.doms.prev  = $(this.container).getElements('.prev')[0];
        this.doms.next = $(this.container).getElements('.next')[0];
        this.doms.end  = $(this.container).getElements('.end')[0];

        this.activePage = 0;
        this.total = this.getTotal();
        this.limit = this.getLimit();
        this.pagesCount = this.getPagesCount();

        this.setupPanel();
        this.refreshPanel();
        this.refreshPage();
    },

    getLimit: function() {
        return parseInt(this.container.getElements('.limit select')[0].get('value'));
    },

    setLimit: function(cnt) {
        this.limit = parseInt(cnt);
        this.pagesCount = this.getPagesCount();

        if (this.activePage > this.pagesCount - 1) {
            this.activePage = this.pagesCount - 1;
        }

        this.setupPanel();
        this.refreshPanel();
        this.refreshPage();
    },

    getTotal: function() {
        return this.storage.getElements('tr').length;
    },

    getPagesCount: function() {
        if (this.limit > 0) {
            return Math.ceil(this.total / this.limit);
        }
            
        return 1;
    },

    controlEnable: function(control, idx) {
        if (!idx) idx = 0;
        $(control).getElements('span')[idx].setStyle('display', 'none');
        $(control).getElements('a')[idx].setStyle('display', 'block');
    },

    controlDisable: function(control, idx) {
        if (!idx) idx = 0;
        $(control).getElements('span')[idx].setStyle('display', 'block');
        $(control).getElements('a')[idx].setStyle('display', 'none');
    },

    setFirst: function(){
        this.activePage = 0;
        this.refreshPanel();
        this.refreshPage();
    },

    setPrev: function(){
        this.activePage = (this.activePage > 0)? this.activePage - 1 : 0;
        this.refreshPanel();
        this.refreshPage();
    },

    setNext: function(){
        this.activePage = (this.activePage < this.pagesCount - 1)? this.activePage + 1 : this.pagesCount - 1;
        this.refreshPanel();
        this.refreshPage();
    },

    setEnd: function(){
        this.activePage = this.pagesCount - 1;
        this.refreshPanel();
        this.refreshPage();
    },

    setPage: function(idx){
        this.activePage = idx;
        this.refreshPanel();
        this.refreshPage();
    },


    setupPanel: function() {

        $(this.pages).set('html', '');
        for (var i=0; i < this.pagesCount; i++) {
            $(this.pages).grab(new Element('span', {
                html: i+1
            }));
            $(this.pages).grab(new Element('a', {
                title: i+1,
                href: "#",
                html: i+1,
                events: {
                    click: (function(idx){
                        return function(){
                            var paginator = $(this).retrieve('paginator');
                            paginator.setPage(idx);
                            return false;
                        }
                    })(i)
                }
            }));
        }

        $(this.doms.start).set('html', '');
        $(this.doms.start).grab(new Element('span', {
            html: 'Start'
        }));
        $(this.doms.start).grab(new Element('a', {
            title: 'Start',
            href: "#",
            html: 'Start',
            events: {
                click: function(){
                    var paginator = $(this).retrieve('paginator');
                    paginator.setFirst();
                    return false;
                }
            }
        }));
          

        $(this.doms.prev).set('html', '');
        $(this.doms.prev).grab(new Element('span', {
            html: 'Prev'
        }));
        $(this.doms.prev).grab(new Element('a', {
            title: 'Prev',
            href: "#",
            html: 'Prev',
            events: {
                click: function(){
                    var paginator = $(this).retrieve('paginator');
                    paginator.setPrev();
                    return false;
                }
            }
        }));

        $(this.doms.next).set('html', '');
        $(this.doms.next).grab(new Element('span', {
            html: 'Next'
        }));
        $(this.doms.next).grab(new Element('a', {
            title: 'Next',
            href: "#",
            html: 'Next',
            events: {
                click: function(){
                    var paginator = $(this).retrieve('paginator');
                    paginator.setNext();
                    return false;
                }
            }
        }));

        $(this.doms.end).set('html', '');
        $(this.doms.end).grab(new Element('span', {
            html: 'End'
        }));
        $(this.doms.end).grab(new Element('a', {
            title: 'End',
            href: "#",
            html: 'End',
            events: {
                click: function(){
                    var paginator = $(this).retrieve('paginator');
                    paginator.setEnd();
                    return false;
                }
            }
        }));

        var self = this;
        this.container.store('paginator', self);
        $(this.container).getElements('a, select').each(function(el){
            el.store('paginator', self);
        });

        $(this.container).getElements('div').each(function(el){
            el.removeClass('off');
        });

        $(this.container).getElements('select')
        .removeEvents('change')
        .setProperty('onchange', false)
        .addEvent('change', function(){
            var paginator = $(this).retrieve('paginator');
            paginator.setLimit($(this).get('value'));
            return false;
        });

    },

    refreshPanel: function () {
            
        if (this.activePage == 0) {
            this.controlDisable($(this.doms.start));
            this.controlDisable($(this.doms.prev));
        } else {
            this.controlEnable($(this.doms.start));
            this.controlEnable($(this.doms.prev));
        }

        if (this.activePage == this.pagesCount-1) {
            this.controlDisable($(this.doms.next));
            this.controlDisable($(this.doms.end));
        } else {
            this.controlEnable($(this.doms.next));
            this.controlEnable($(this.doms.end));
        }

        for (var i=0; i < this.pagesCount; i++) {
            if (i == this.activePage) {
                this.controlDisable($(this.pages), i);
            } else {
                this.controlEnable($(this.pages), i);
            }
        }

        $(this.container).getElements('.limit')[1].set('html', 'Page ' + (this.activePage + 1) + ' of ' + this.pagesCount);
    },

    refreshPage: function () {

        var start = (this.limit==0)? 0 : this.activePage * this.limit;
        var end   = (this.limit==0)? this.total : start + this.limit;
        this.storage.getElements('tr').each(function(row, idx){
            if (idx >= start && idx < end) {
                $(row).removeClass('paginated-hide');
            } else {
                $(row).addClass('paginated-hide');
            }
        });
    },

    refresh: function() {
        this.refreshPage();
        this.refreshPanel();
    }
});

/**
 * Retrieve the item from iterable object
 *
 * @param dataset  - the iterable object
 * @param keyField - the name of item's field for search
 * @param value    - the value of item's field to find
 * @param link     - clone the result or not
 *
 * @return object
 * @since  1.0
 */
Migur.iterator.getItem = function(dataset, keyField, value, link) {
    var res = [];
    for(var i=0; i < dataset.length; i++) {
        if (dataset[i][keyField] && dataset[i][keyField] == value) {
            // Default behaviour is to clone the esults
            if (!link) {
                res = [dataset[i]].clone()[0];
            } else {
                res = dataset[i];
            }
        }
    }
    return res;
}

Migur.setRadio = function(dom, value) {
    var radios = $(dom).getElements('input');

    // get all radios
    radios.each(function(radio) {
        if (radio.get('value') == value) {
            radio.setProperty('checked' ,true);
        }
    });
}

Migur.validator = {

    /**
     * Fires tab inside of which the invalid field is placed.
     * @param container - css-selector of a container
     * @param tabElement - css-selector of an element inside the tab which css-style will be changed
     * @param markerClass - css-class that applies to tabElement
     * @param needle - css-selector that identifies the invalid field
     *
     * @return void
     * @since  1.0
     * 
     * Usage: tabIndicator('#tabs-list', '.tabs', 'span h3 a', '.tab-invalid', '.invalid');
     */
    tabIndicator: function(container, tabElement, markerClass, needle){

        var tabs  = $$(container + ' > dl.tabs > dt.tabs');
        var panes = $$(container + ' > div.current > dd.tabs');

		if (!tabs || !panes) return;

		// Reset styles for each tab
		tabs.each(function(el){
			el.getElement(tabElement).removeClass(markerClass);
		});

		// Set styles
        panes.each(function(el, idx){

			if (el.getElement(needle)) {
				tabs[idx].getElement(tabElement).addClass(markerClass);
			}	
        });
    },

    sliderIndicator: function(container, tabElement, markerClass, needle){

        var tabs  = $$(container + ' > .panel');
        var panes = $$(container + ' > .panel > div');

		if (!tabs || !panes) return;

		// Reset styles for each tab
		tabs.each(function(el){
			el.getElement(tabElement).removeClass(markerClass);
		});

		// Set styles
        panes.each(function(el, idx){

			if (el.getElement(needle)) {
				tabs[idx].getElement(tabElement).addClass(markerClass);
			}
        });
    }
}


/**
 * Helps to create long event-based multistep operations
 */
Migur.multistepProcess = function(){
		
	this.data = {};
		
	this.begin = function(){};
	
	this.step = function(){};
	
	this.end = function(){};
	
	this.processResult = function() {
		if(data) {
			this._data.push(data);
			return true;
		}
		
		return false;
	};
	
	this.onComplete = function(res) {
		if (this.processResult(res) == false) {
			this.end(res);
		} else {
			this.step();
		}
	};
	
	this.start = function(data){
		this.begin(data);
		this.step();
	};
}
