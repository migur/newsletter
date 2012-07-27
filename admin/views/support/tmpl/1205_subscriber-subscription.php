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
<br/>

<h3>Subscription to a list</h3>
<p>Users have an ability to subscribe to one or several lists themselves. They can do it with help of Migur Subscription module. Since user performed a subscription to a list he will get newsletters.
To subscribe a user have to provide name, email and type of letters to receive in coresponding fields of a module. After submitting this data the subscription process begins. Component supports 2 modes of subscription.
<ul>
    <li>Subscription via newsletter</li>
    <li>Autoconfirmation</li>
</ul>
<p>Administrator can select a mode for each list. The “Subscription via newsletter” is the default behavior. To select “Autoconfirmation” administrator need to check “Autoconfrim users, do not send confirmation email” in List -> Advanced.
<br/>

<a name="regmail"></a>
<h4>Subscription via newsletter</h4>
Subscription newsletter is used to:
<ul>
    <li>Provide information about a list.</li>
    <li>Provide confirmation link to confirm the subscription to this list.</li>
</ul>
<p>User will receive confirmation letter selected in options of a list (List -> Advanced -> Send newsletter at registration). If no confirmation letter selected in list then user will get default subscription letter(see Configuration -> Global -> Newsletters -> The subscription newsletter). If this letter does not specified then the fallback newsletter will be used (Configuration -> Global -> General -> “Subject of confirmation letter” and “Body of confirmation letter”).
<p>If user is logged in as J! user and provided email matches with his J! email or user is logged in Facebook and provide email matches with his Facebook email then subscriber will be created/updated as confirmed. Mails will not be sent. 
<p>In other cases subscriber will be created as unconfirmed. A subscription mail will be sent to provided email for approving his subscription. He will receive so many letters 