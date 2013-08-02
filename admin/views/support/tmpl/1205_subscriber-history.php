<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="content">
<h1>The subscriber History - Help page</h1>
<br/><br/>
In <b>History</b> block you can see all the actions of a subscriber.
The component can observe these subscriber's events:
<ul>
	<li>Signed up to a list</li>
	<li>Unsubscribed from a list</li>
	<li>Sent</li>
	<li>Opened</li>
	<li>Clicked link</li>
	<li>Bounced</li>
</ul>

<p><b>Signed up to a list</b> event occurs on subscriber's subscription to some list(s).
This type of history does not added if admin assigns subscriber to a list. It logs only subscriptions to a list 
that performed by subscriber himself via Subscription Module.
	
<p><b>Unsubscribed from a list</b> event occurs on subscriber's unsubscription from some list(s).
This type of history does not added if admin removes subscriber from a list. It logs only unsubscriptions from a list 
that performed by subscriber himself via Subscription Module.

<p><b>Sent</b> event occurs on every sending of a mail to this subscriber.
	
<p><b>Opened</b> event occurs when subscriber open the mail in his mailbox. 
Please note that we can track <b>Opened</b> events only for letters that have <b>HTML</b> type. 
Because there is no way to add any elements into PLAIN letter to track (images, etc.).
event loggs only once for each letter at first opening of a mail.
This type of events tracked on mails sent as preview letter as well.

<p><b>Clicked link</b> event occurs when subscriber click some link in the mail he received. 
There you can find information about of what link has been clicked. This event logs only once for each link.
This type of events tracked on mails sent as preview letter as well.

<p><b>Bounced</b>. 
This record will be added to history if system find out that the mail has been sent to this user was bounced.
For more details see Bounces Check Help.

</div>
