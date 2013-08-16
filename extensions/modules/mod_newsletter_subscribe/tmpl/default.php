<?php
/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die; ?>

<?php if (!empty($list)) { ?>

<?php if (!empty($userEmail)) echo '<!--{emailcloak=off}-->'; ?>

<?php if ($showFb) { ?>
	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
	<script>
		FB.init({
			appId:'<?php echo $fbappid; ?>',
			cookie:true,
			status:true,
			xfbml:true
		});

		FB.Event.subscribe('auth.authResponseChange', function(response) {
			if (response.status === 'connected') {
				FB.api('/me', function(response) {
					if (response.name && response.email) {
						$$('[name="newsletter-name"]').set('value', response.name);
						$$('[name="newsletter-email"]').set('value', response.email);
					}
				});
			}
		});
	</script>
<?php } ?>

<div class="newsletter<?php echo $moduleclass_sfx; ?>">

    <?php if ($params->get('textprepend', '') != '') { ?>
    <div class="newsletter-prepend-text">
        <?php echo $params->get('textprepend', ''); ?>
    </div>
    <?php } ?>

	<form class="mod-newsletter" action="?option=com_newsletter" method="POST" name="subscribe-form">

        <?php if ($params->get('textabovename', '') != '') { ?>
        <div class="mod-newsletter-subscribe-container newsletter-text-name">
            <?php echo $params->get('textabovename', ''); ?>
        </div>
        <?php } ?>

        <div class="mod-newsletter-subscribe-container">
			<input class="required validate-newsletter-name inputbox newsletter-name" name="newsletter-name" type="text" size="20" value="<?php echo $userName; ?>" />
		</div>

        <?php if ($params->get('textaboveemail', '') != '') { ?>
        <div class="mod-newsletter-subscribe-container newsletter-text-email-above">
            <?php echo $params->get('textaboveemail', ''); ?>
        </div>
        <?php } ?>

		<div class="mod-newsletter-subscribe-container">
			<input class="required validate-newsletter-email inputbox newsletter-email" name="newsletter-email" type="text" size="20" value="<?php echo $userEmail; ?>" />
		</div>

        <?php if ($params->get('textunderemail', '') != '') { ?>
        <div class="mod-newsletter-subscribe-container ewsletter-text-email-under">
            <?php echo $params->get('textunderemail', ''); ?>
        </div>
        <?php } ?>

		<?php if($showFb) { ?>
		<div class="mod-newsletter-subscribe-container">
			<fb:login-button perms="email"></fb:login-button>
		</div>
		<?php } ?>

        <?php if ($params->get('showmailtype', 1) == 1) { ?>
		<div class="mod-newsletter-subscribe-container newsletter-html-container">
			<h4><?php echo JText::_('MOD_NEWSLETTER_RECIEVE'); ?></h4>
			<?php echo JHTML::_('select.radiolist', $radios, 'newsletter-html', array('class' => 'required'), 'value', 'text', $params->get('defaultmailtype', 1)); ?>
		</div>
        <?php } else { ?>
            <input type="hidden" name="newsletter-html" value="<?php echo $params->get('defaultmailtype', 1); ?>" />
        <?php } ?>

		<div class="mod-newsletter-subscribe-container">
			<?php if (count($list) > 1) { ?>
			<h4 for="newsletter-lists"><?php echo JText::_('MOD_NEWSLETTER_SELECT_LIST_TO_SUBSCRIBE'); ?></h4>
				<select name="newsletter-lists[]" class="inputbox required">
					<?php echo JHtml::_('select.options', $list, 'value', 'text', 0, true);?>
				</select>
			<?php } else { ?>
				<div>
					<?php echo JText::_('MOD_NEWSLETTER_LIST_TO_SUBSCRIBE'); ?><br>
					<b><?php echo !empty($list[0]->text)? $list[0]->text : ''; ?></b>
				</div>
				<input name="newsletter-lists[]" type="hidden" value="<?php echo $list[0]->value; ?>" />
			<?php } ?>
		</div>

		<?php if ($params->get('showtermslink', false)) { ?>
		<div class="mod-newsletter-subscribe-container">
			<div id="newsletter-terms" class="required checkboxes">
				<label id="newsletter-terms-container" class="checkbox">
					<input id="newsletter-terms0" class="validate-newsletter-terms" name="newsletter-terms" type="checkbox" />
					<a	rel="{handler: 'iframe', size: {x: 820, y: 400} }"
						class="modal"
						href="<?php echo $termslink; ?>"
					>
						<?php echo JText::_('MOD_NEWSLETTER_TERMS_AND_CONDITIONS'); ?>
					</a>
				</label>
			</div>
		</div>
		<?php } ?>

		<div id="newsletter-submit-container" class="mod-newsletter-subscribe-container">
			<input
				type="button"
				value="<?php echo JText::_('MOD_NEWSLETTER_SUBSCRIBE'); ?>"
				onClick="modNewsletterSubmit(this)"
				class="btn btn-success"
			/>
		</div>

        <?php if ($params->get('textappend', '') != '') { ?>
        <div class="mod-newsletter-subscribe-container newsletter-append-text">
            <?php echo $params->get('textappend', ''); ?>
        </div>
        <?php } ?>

		<input type="hidden" name="fbenabled" value="<?php echo $params->get('fbenabled'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>

<script text="javascript">
	migurSiteRoot = "<?php echo JUri::root(); ?>";
	migurName = "<?php echo $userName; ?>";
	migurEmail = "<?php echo $userEmail; ?>";
</script>

<?php } else { ?>

	<span><?php echo JText::_('MOD_NEWSLETTER_SUBSCRIBE_NO_LISTS_TO_SUBSCRIBE'); ?></span>

<?php } ?>
