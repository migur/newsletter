<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="content">
<h1>SMTP configuration - Help page</h1>
<br/><br/>
<p>Sometimes those who manage a newsletter might use more than one Internet service provider. 
Component has SMTP profiles that allows you to manage your newsletters more flexible. 
SMTP profile is a bunch of settings to connect to SMTP server and additional parameters used in component. 
When you select some SMTP profile in newsletter (or list) you order that this 
SMTP connection will be used to send this newsletter. 
To add, edit or assign profile as default go to Configuration -> General -> Default SMPT profile.

<p>SMTP profile has these options:
<ul>
	<li>Name - is the verbal name of a profile</li>
	<li>From name - name used as FROM NAME.</li>
	<li>From email - email used as FROM EMAIL. Note that for most servers it should be identical to email of your SMTP server account.</li>
	<li>Reply to name - name used as REPLY TO NAME</li>
	<li>Reply to email - email used as REPLY TO EMAIL. Note that for most servers it should be identical to email of your SMTP server account.</li>
	<li>SMTP server - URL of smtp server</li>
	<li>SMTP port - port being used</li>
	<li>User name - email address of your SMTP account</li>
	<li>User password - password of your SMTP account</li>
	<li>Encryption method (No/SSL/TLS) - create connection without encription or using SSL or TLS.</li>
	<li>Mailbox profile - mailbox profile that will be used to check bounced mails that has been sent through this SMTP profile.</li>
	<li>Count of mails will be sent in each mailing period - amount of mails that will be sent in each sending iteration. Used in CRON mailing.</li>
	<li>Length of mailing period (in minutes) - the span of period.</li>
</ul>
<br/>

<p>Some of SMTP servers have the limitation of amount of sent mails per time interval. 
The  “Count of mails will be sent in each mailing period” and “Length of mailing period (in minutes)” 
options are intended to tune up a SMTP profile to fit into these restrictions.

<a name="smtpp-joomla"></a>
<h3>Joomla profile</h3>
<p>Component can use Joomla SMTP profile as its own ones. 
You can only manage “Count of mails will be sent in each mailing period” and 
“Length of mailing period (in minutes)” for this profile. 
To edit other options please use standard J! functionality.
</p>

<a name="smtpp-default"></a>
<h3>Default SMTP profile</h3>
<p>You can assign one SMTP profile (J!’s or component’s) as default SMTP profile. 
This profile will be used by default. Also It is flexible so you can change 
the SMTP profile used in several newsletters in one step. 
By assigning another SMTP profile as default.
</p>

<a name="smtpp-list"></a>
<h3>List SMTP profile overridding</h3>
<p>If newsletter that you sent to the list uses a default profile then 
you can override it in list’s settings with help of "Default - SMTP profile" option for this list.
</p>

</div>
