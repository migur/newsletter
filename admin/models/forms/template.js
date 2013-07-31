/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

window.addEvent('domready', function() {

    /*
     * The validators for form fields
     */
    document.formvalidator.cssdimensionValidator = function (value) {
        regex = /^([0-9]+\s*(\%|px|em))|auto$/;
        return regex.test(value);
    };

    document.formvalidator.csscolorValidator = function (value) {
        regex = /^\#([0-9abcdefABCDEF]{6}|[0-9abcdefABCDEF]{3})$/;
        return regex.test(value);
    };

    document.formvalidator.simpleTextValidator = function (value) {
        regex=/^.{1,255}$/;
        return regex.test(value);
    };


    document.formvalidator.setHandler('title',    document.formvalidator.simpleTextValidator);
    document.formvalidator.setHandler('image_top',        document.formvalidator.simpleTextValidator);
    document.formvalidator.setHandler('image_top_alt',    document.formvalidator.simpleTextValidator);
    document.formvalidator.setHandler('image_bottom',     document.formvalidator.simpleTextValidator);
    document.formvalidator.setHandler('image_bottom_alt', document.formvalidator.simpleTextValidator);


    document.formvalidator.setHandler('width_column1',    document.formvalidator.cssdimensionValidator);
    document.formvalidator.setHandler('height_column1',   document.formvalidator.cssdimensionValidator);
    document.formvalidator.setHandler('width_column2',    document.formvalidator.cssdimensionValidator);
    document.formvalidator.setHandler('height_column2',   document.formvalidator.cssdimensionValidator);
    document.formvalidator.setHandler('image_top_width',  document.formvalidator.cssdimensionValidator);
    document.formvalidator.setHandler('image_top_height', document.formvalidator.cssdimensionValidator);
    document.formvalidator.setHandler('image_bottom_width',  document.formvalidator.cssdimensionValidator);
    document.formvalidator.setHandler('image_bottom_height', document.formvalidator.cssdimensionValidator);

    document.formvalidator.setHandler('table_background', document.formvalidator.csscolorValidator);
    document.formvalidator.setHandler('text_color',       document.formvalidator.csscolorValidator);
});