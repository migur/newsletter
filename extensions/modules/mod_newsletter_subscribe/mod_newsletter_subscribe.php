<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$userName = modNewsletterSubscribeHelper::getName();
$userName = empty($userName) ? JText::_('MOD_NEWSLETTER_NAME') : $userName;

$userEmail = modNewsletterSubscribeHelper::getEmail();
$userEmail = empty($userEmail) ? JText::_('MOD_NEWSLETTER_EMAIL') : $userEmail;

$list = modNewsletterSubscribeHelper::getList($params);
$radios = modNewsletterSubscribeHelper::getType($params);
$termslink = $params->get('termslink', '');

$sendto = modNewsletterSubscribeHelper::getSendToURL($params);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

JHTML::_('behavior.modal');
JHtml::_('behavior.framework');
JHtml::_('behavior.formvalidation');
modNewsletterSubscribeHelper::addHeadData();

$fbMe = '';
$fbappid = $params->get('fbappid', false);
$fbsecret = $params->get('fbsecret', false);
//'255005257848916', 'e3b0efe6fc9bd842f50f339ea42e575a'
if (!empty($fbappid) && !empty($fbsecret)) {
	$fbMe = modNewsletterSubscribeHelper::getFbMe($fbappid, $fbsecret);
}

$showFb = $fbappid && $fbsecret && $params->get('fbenabled', false) && empty($fbMe->email);

require JModuleHelper::getLayoutPath('mod_newsletter_subscribe', $params->get('layout', 'default'));
