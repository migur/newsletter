<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Button Field class for the Migur Framework.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class JFormFieldEditorsimple extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.0
	 */
	protected $type = 'button';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.0
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$class		= $this->element['class'] ? ' class="mce_editable_small '.(string) $this->element['class'].'"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$columns	= $this->element['cols'] ? ' cols="'.(int) $this->element['cols'].'"' : '';
		$rows		= $this->element['rows'] ? ' rows="'.(int) $this->element['rows'].'"' : '';
		$width      = $this->element['width']  ? $this->element['width']  : '200';
		$height     = $this->element['height'] ? $this->element['height'] : '200';

		// Initialize JavaScript field attributes.
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';



		$html = '<textarea name="'.$this->name.'" id="'.$this->id.'"' .
				$columns.$rows.$class.$disabled.$onchange. ' width="' . $width. 'px" height="' .$height.'px">' .
				htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') .
				'</textarea>';

		$html .= '<script type="text/javascript" src="' . JURI::root() . 'media/editors/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>';

		$html .=
			'<script type="text/javascript">
				tinyMCE.init({
					// General
					directionality: "ltr",
					editor_selector : "mce_editable_small",
					language : "en",
					mode : "specific_textareas",
					skin : "default",
					theme : "advanced",
					// Cleanup/Output
					inline_styles : true,
					gecko_spellcheck : true,
					cleanup : true,
					cleanup_on_startup : false,
					entity_encoding : "raw",
					extended_valid_elements : "hr[id|title|alt|class|width|size|noshade|style],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],a[id|class|name|href|target|title|onclick|rel|style]",
					force_br_newlines : false, force_p_newlines : true, forced_root_block : \'p\',
					invalid_elements : "script,applet,iframe",
					// URL
					relative_urls : true,
					remove_script_host : false,
					document_base_url : "' .  JURI::root() . '/",
					// Layout
					content_css : "' .  JURI::root() . 'templates/system/css/editor.css",
					// Advanced theme
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_buttons1 : "formatselect",
					theme_advanced_buttons2 : "",
					theme_advanced_buttons3 : "",

					theme_advanced_source_editor_height : "' . $height . '",
					theme_advanced_source_editor_width : "' . $width . '",
					theme_advanced_resize_horizontal : true,
					theme_advanced_resizing : true,
					theme_advanced_statusbar_location : "bottom", theme_advanced_path : true
				});
				</script>';
		return $html;

	}

	/**
	 * Method to get the field options.
	 *
	 * @return	null
	 * @since	1.0
	 */
	protected function getOptions()
	{
		return null;
	}

}
