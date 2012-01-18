<?php
/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die; ?>

<?php if ($showFb) { ?>
	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
	<script>
	 FB.init({
		appId:'<?php echo $params->get('fbappid'); ?>', cookie:true,
		status:true, xfbml:true
	 });

	</script>
<?php } ?>

<div class="newsletter<?php echo $moduleclass_sfx; ?>">
	<form class="mod-newsletter" action="?option=com_newsletter" method="POST" name="subscribe-form">
		<div>
			<input class="required validate-newsletter-name inputbox newsletter-name" name="newsletter-name" type="text" size="20" value="<?php echo $userName; ?>" />
		</div>
		<div>
			<input class="required validate-newsletter-email inputbox newsletter-email" name="newsletter-email" type="text" size="20" value="<?php echo $userEmail; ?>" />
		</div>

		<?php if($showFb) { ?>
		<div>
			<fb:login-button perms="email"></fb:login-button>
		</div>
		<?php } ?>
			
		<fieldset>
			<label for="newsletter-html"><?php echo JText::_('MOD_NEWSLETTER_RECIEVE'); ?></label>
			<?php echo JHTML::_('select.radiolist', $radios, 'newsletter-html', array('class' => 'required'), 'value', 'text', '1'); ?>
		</fieldset>
		<div>
			<?php if (count($list) > 1) { ?>
			<label for="newsletter-lists"><?php echo JText::_('MOD_NEWSLETTER_SELECT_LIST_TO_SUBSCRIBE'); ?></label>
				<select name="newsletter-lists[]" multiple size="5" class="inputbox required">
					<?php echo JHtml::_('select.options', $list, 'value', 'text', 0, true);?>
				</select>
			<?php } else { ?>
				<div>
					<?php echo JText::_('MOD_NEWSLETTER_LIST_TO_SUBSCRIBE'); ?><br>
					<b><?php echo $list[0]->text; ?></b>
				</div>
				<input name="newsletter-lists[]" type="hidden" value="<?php echo $list[0]->value; ?>" />
			<?php } ?>
		</div>
		<div>
			<fieldset id="newsletter-terms" class="required checkboxes">
				<div id="newsletter-terms-container"><input id="newsletter-terms0" class="validate-newsletter-terms" name="newsletter-terms" type="checkbox" /></div>
				<a	rel="{handler: 'iframe', size: {x: 820, y: 400} }"
					class="modal"
					href="<?php echo $termslink; ?>"
				>
					<?php echo JText::_('MOD_NEWSLETTER_TERMS_AND_CONDITIONS'); ?>
				</a>
			</fieldset>
		</div>
		<div>
			<input
				type="button"
				value="<?php echo JText::_('MOD_NEWSLETTER_SUBSCRIBE'); ?>"
				onClick="modNewsletterSubmit(this)"
			/>
		</div>

		<input type="hidden" name="fbenabled" value="<?php echo $params->get('fbenabled'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>

<script text="javascript">
	migurSiteRoot = "<?php echo JUri::root(); ?>";
	migurName = "<?php echo $userName; ?>";
	migurEmail = "<?php echo $userEmail; ?>";
</script>