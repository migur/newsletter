<?php
/**
 * @package    Joomla.Test
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class to mock JSession.
 *
 * @package  Joomla.Test
 * @since    12.1
 */
class NewsletterTestMockUser
{
	public $defaultAuthoriseReturn;
	
	public function authorise()
	{
		return $this->defaultAuthoriseReturn;
	}
}
