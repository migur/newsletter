<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="content">
<h1>Subscriber list - Help page</h1>
<br/><br/>
Subscribers are main target of a component. 
There are 2 types of subscribers:
<ul>
<li>Migur subscriber</li>
<li>Joomla! user</li>
</ul>
The definition key of a subscriber is his email. Admin can manage the list of subscribers on a subscribers page. The subscribers list allows administrator to look through and manage subscribers. Also it allows to manage the inclusion of subscribers into subscribers lists. There you can see and use both Joomla!users and Migur subscribers. Joomla! user is standard Joomla! user that you can manage via Joomla! user manager.
<h4>Toolbar</h4>
<p><b>Remove from list</b> button allows you to remove selected subscriber(s) from list(s) selected in "Lists" panel.
<p><b>Add to list</b> button allows you to add selected subscriber(s) to list(s) selected in "Lists" panel.
<p><b>New</b> button allows you to create new Migur subscriber filling its name and email. To create Joomla! user please use Joomla! user manager.
<p><b>Delete</b> button allows you to delete Migur subscriber. To delete Joomla! user please use Joomla! user manager.
<p><b>Enable</b> button allows you to enable subscriber/Joomla! user. See below.
<p><b>Disable</b> button allows you to disable subscriber/Joomla! user. See below.
<p><b>Activate</b> button appears only if you are looking through subscribers of particular list (particular list is selected in "List" filter). This button allows you to activate a subscription of a subscriber to a selected list manually.
<p><b>Help</b> button opens this help page.</p>

<a name="enebling"></a>
<h4>Subscriber enabling/disabling</h4>
This feature is similar to enabling/disabling feature in Joomla! user manager. 
The disabling of a subscriber means that no emails will be sent to him from all 
lists he subscribed or added by admin. It has nothing to do with disabling Joomla! user. 
All the subscribers are enabled by default. Administrator can disable subscribers for some 
reason (see Activation and disabling in mailing process).

<a name="activation"></a>
<h4>Subscriber activation</h4>
Activation is the process of confirmation subscriber’s subscription to particular list. Subscriber becomes activated for particular list when he:
<ul>
	<li>confirm a subscription via email</li>
	<li>subscribed to a list with autoconfirmation</li>
</ul>	
It is similar to activation feature in Joomla! user manager. But Migur activation is not for all system but for particular list. It is the main difference. Subscriber does not confirm himself or his email. He confirms his subscription to a list. It has nothing to do with activation of Joomla! user. Does not matter if Joomla! user is active or not. Only activation of a subscriber to a particular list has sense. Inactive status shows us that user did a subscription but just not proved it with help of email yet. Admin may want to activate it himself to be able to send something to that subscriber.

<h4>Activation and disabling in mailing process</h4>
When administrator invokes the mailing a newsletter to some list then all the subscribers from that list are added to mailing queue along with inactive or disabled ones. But newsletters will be sent to only enabled subscribers that activated for that list. Queue items that related to disabled or inactive subscribers will be pended till appropriate subscribers will be enabled or confirm their subscription for appropriate list. So we can say that admin can activate a subscriber manually to be able to send to him something and can disable him to prevent the mailing to him.
Let’s consider an example:
Once administrator mailed (added to queue) a newsletter to a list that has some both inactive and disabled subscribers. After that administrator enabled some disabled subscribers from that list. After that action mailer will send a newsletter to these subscribers. In addition if some subscribers will confirm their subscriptions to that list then they will receive their newsletters too.
</div>
