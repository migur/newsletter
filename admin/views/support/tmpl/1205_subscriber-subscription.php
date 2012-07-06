<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="content">
<h1>The subscription and confirmation of a subscribers - Help page</h1>
<br/><br/>
There are several ways to add the subscriber into component.
<ul>
	<li>Via “Create subscriber” in admin’s dashboard or “New” on “Subscribers” page. Subscriber will be created as confirmed. No emails will be sent.</li>
    <li>Via importing from CSV or other sources (using third party plugins) in “List” window. Subscriber will be created as confirmed. No emails will be sent.</li>
    <li>User can perform the subcription via Migur Subscription module (mod_newsletter_subscribe).</li>
</ul>
<p>To subscribe user have to provide name, email and type of letters to receive.

<h3>SUBSCRIPTION NEWSLETTER</h3>
<p>To be able to get newsletters of certain list subscriber should be subscribed to this list and his subscription will be confirmed. The purpose of a Subscription newsletter is to provide information about a list and confirmation link to confirm the subscription to it.
However if option "Autoconfirm users" (Configuration -> Global -> General) is set then user will be created as confirmed and without confirmation.
User receives confirmation letter selected in options of a list (List -> Advanced -> Send newsletter at registration).
If no confirmation letter selected in list then user will get default subscription letter(see Configuration -> Global -> Newsletters -> The subscription newsletter). If this letter does not specified too then the fallback newsletter will be used (Configuration -> Global -> General -> “Subject of confirmation letter” and “Body of confirmation letter”).

<h3>SUBSCRIPTION VIA MODULE</h3>
<p>There are several cases:
<ul>	
<li>1. If user is logged in as J! user and provided email matches with his J! email
  then subscriber created/updated as confirmed.
  In addition all assigns to lists created/updated as confirmed.
  Mails will not be sent.</li>
 
<li>2. If user is logged in Facebook an provided email matches with his Facebook email
  then see 1.</li>

<li>3. In other cases subscriber will be created as unconfirmed.
  He will recieve mail to email provided in module's form with confirmation link.
  He will receive so many letters as lists he subscribed. The letters will be sent immediately.</li>
</ul>

<h3>REGISTRATION DISABLING</h3>
<p>If "Enable Registration" admin option is set to NO then registration is not posible. 
<br/>	
<h3>AUTOMAILING</h3>
<p>
	<a href="<?php echo SupportHelper::getResourceUrl('automailing'); ?>">Automailing</a> will be performed on subscription of a user if there is one for list to which user subscribed. 
	To send automailing newsletters you need CRON. See the <a href="<?php echo SupportHelper::getResourceUrl('cron'); ?>">CRON section</a>.	
</div>
