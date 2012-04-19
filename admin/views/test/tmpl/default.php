<?php
// no direct access
defined('_JEXEC') or die;
?>

<table class="templateslist adminlist" width="100%">
	<thead>
		<tr>
			<th>Action</th>
			<th>Method</th>
			<th>URL</th>
			<th>params</th>
			<th>additional</th>
		</tr>
	</thead>
	
	<tfoot>
		<tr>
			<td colspan="5">
			</td>
		</tr>
	</tfoot>

	<tbody>
		<tr class="row0">
			<td><a class="test-title" href="#">Subscription</a></td>
			<td><select class="test-method"><option value="get">GET</option><option selected value="post">POST</option></td>
			<td><input class="test-url" value="<?php echo 'index.php?option=com_newsletter&task=subscribe.subscribe'; ?>" size="60"/></td>
			<td><input class="test-params" value="<?php echo 'newsletter-name=NameOfUser&newsletter-email=email@ofuser.com&newsletter-html=1&newsletter-lists=&fbenabled=0'; ?>" size="80" /></td>
			<td><input class="test-other" value="<?php echo JUtility::getToken(); ?>=1" size="60" /></td>
		</tr>
		<tr class="row1">
			<td><a class="test-title" href="#">Cron.Automailing</a></td>
			<td><select class="test-method"><option selected value="get">GET</option><option value="post">POST</option></td>
			<td><input class="test-url" value="<?php echo 'index.php?option=com_newsletter&task=cron.automailing'; ?>" size="60"/></td>
			<td><input class="test-params" value="" size="80" /></td>
			<td><input class="test-other" value="" size="60" /></td>
		</tr>
		<tr class="row0">
			<td><a class="test-title" href="#">Cron.Mailing</a></td>
			<td><select class="test-method"><option selected value="get">GET</option><option value="post">POST</option></td>
			<td><input class="test-url" value="<?php echo 'index.php?option=com_newsletter&task=cron.mailing'; ?>" size="60"/></td>
			<td><input class="test-params" value="" size="80" /></td>
			<td><input class="test-other" value="" size="60" /></td>
		</tr>
		<tr class="row1">
			<td><a class="test-title" href="#">Logs.Automailing</a></td>
			<td><select class="test-method"><option selected value="get">GET</option><option value="post">POST</option></td>
			<td><input class="test-url" value="<?php echo '/logs/automailing/'.date('Y-m-d').'.txt'; ?>" size="60"/></td>
			<td><input class="test-params" value="" size="80" /></td>
			<td><input class="test-other" value="" size="60" /></td>
		</tr>
		<tr class="row0">
			<td><a class="test-title" href="#">Logs.Cron</a></td>
			<td><select class="test-method"><option selected value="get">GET</option><option value="post">POST</option></td>
			<td><input class="test-url" value="<?php echo '/logs/cron/'.date('Y-m-d').'.txt'; ?>" size="60"/></td>
			<td><input class="test-params" value="" size="80" /></td>
			<td><input class="test-other" value="" size="60" /></td>
		</tr>
		<tr class="row1">
			<td><a class="test-title" href="#">Logs.Mailer</a></td>
			<td><select class="test-method"><option selected value="get">GET</option><option value="post">POST</option></td>
			<td><input class="test-url" value="<?php echo '/logs/mailer/'.date('Y-m-d').'.txt'; ?>" size="60"/></td>
			<td><input class="test-params" value="" size="80" /></td>
			<td><input class="test-other" value="" size="60" /></td>
		</tr>
		<tr class="row0">
			<td><a class="test-title" href="#">Add users</a></td>
			<td><select class="test-method"><option selected value="get">GET</option><option value="post">POST</option></td>
			<td><input class="test-url" value="<?php echo 'administrator/index.php?option=com_newsletter&task=test.addFakeUsers'; ?>" size="60"/></td>
			<td><input class="test-params" value="count=10000&start=1&prefix=ZZ Test user" size="80" /></td>
			<td><input class="test-other" value="" size="60" /></td>
		</tr>
		<tr class="row1">
			<td><a class="test-title" href="#">Add subscribers</a></td>
			<td><select class="test-method"><option selected value="get">GET</option><option value="post">POST</option></td>
			<td><input class="test-url" value="<?php echo 'administrator/index.php?option=com_newsletter&task=test.addFakeSubscribers'; ?>" size="60"/></td>
			<td><input class="test-params" value="count=10000&start=1&prefix=ZZ Test Subscriber" size="80" /></td>
			<td><input class="test-other" value="" size="60" /></td>
		</tr>
	</tbody>
</table>

<input type="button" id="result-type" value="HTML/TEXT" />

<div class="test-result test-html" style="height:600px; background-color: #111111; color: #00FF00; padding: 10px; font-size: 12px; overflow: auto;"></div>
<textarea class="test-text" style="height:600px; width:98%; background-color: #111111; color: #00FF00; padding: 10px; font-size: 12px; overflow: auto; display: none;"></textarea>
