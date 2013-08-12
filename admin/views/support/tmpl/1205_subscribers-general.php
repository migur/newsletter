<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="content">
<h1>Subscribers - Help page</h1>
<br/>
<p>The Administrator manages Subscribers and mailing Lists on the Subscribers page. There you find a list of all the Subscribers and a list of all the mailing Lists that the Subscribers can be associated with. The definition key of a Subscriber is his email address.
Subscribers are the main target of the component. 
There are two types of Subscribers (represented by different icons):
<ul><li>Joomla! user</li>
<li>Migur subscriber</li></ul>
<p><b>Joomla! user</b> is the standard Joomla! user that is managed via Joomla! User Manager. All Joomla! users are automatically included in the Subscribers list. Disabling a Joomla! user in the Migur Subscriber list does not disable them in the Joomla! User Manager and disabling them in the Joomla! User Manager does not disable them in the Migur Subscriber list.
<p><b>Migur subscribers</b> are added to the Migur Subscriber list through subscription registration modules, imported lists or manually adding a subscriber.
<p>All Subscribers are automatically Enabled in the Subscriber list. Activation is associated to a particular mailing list and can be set by using Autoconfirmation (in the mailing list settings) or require the Subscriber to confirm their request to a mailing list via email verification. Also the administrator can activate the Subscriber manually for a mailing list. More about Enabled and Activation below.
<h4>Toolbar</h4>
<b>New subscriber</b> button to create a new Migur Subscriber by filling their name and email. To create Joomla! user please use Joomla! user manager. They will automatically be added to this list.
<br/><b>Delete</b> button allows you to delete Migur subscriber. To delete Joomla! user please use Joomla! user manager.
<br/><b>Enable</b> button allows you to enable a subscriber/Joomla! user. See below.
<br/><b>Disable</b> button allows you to disable a subscriber/Joomla! user. See below.
<br/><b>Remove from list</b> button removes the selected Subscriber(s) from the mailing List(s) selected in "Lists" panel. At least one Subscriber and one List must be checked.
<br/><b>Asign to list</b> button adds the selected Subscriber(s) to the mailing List(s) selected in "Lists" panel. At least one Subscriber and one List must be checked.
<br/><b>Activate</b> button appears only if you are viewing subscribers of a particular mailing list (the particular list is selected in "List" filter). This button allows you to activate the subscription of the Subscriber to that selected mailing List manually.
<h4>Subscriber enabling/disabling</h4>
<p>This feature is similar (but not connected) to enabling/disabling feature in the Joomla! User Manager. The disabling of a Subscriber means that no emails will be sent to them from any list they are subscribed to or added to by the Administrator. It has nothing to do with disabling a Joomla! user. All the subscribers are enabled by default. The Administrator is able to disable subscribers if need (see Activation and disabling during mailing process).
<h4>Subscriber activation</h4>
<p>Activation is the process of confirming a Subscriber’s subscription to particular mailing list. The Subscriber becomes activated for a particular List when he:
<ul><li>confirm a subscription via email</li>
<li>subscribed to a list with autoconfirmation</li>
<li>the administrator can also activate the Subscriber manually if necessary</li>
</ul>
<p>It is similar to the activation feature in the Joomla! User Manager. However Migur activation is not related to the Joomla! system but only to a particular mailing List in Migur. When a Subscriber confirms his subscription to a mailing list, they set the status of their relationship to this list to Active. They can not change their Enabled status and it has nothing to do with their Joomla! User status if they are a Joomla! user. An Inactive status shows us that the Subscriber registered for a subscription to a mailing list but has not confirmed it via email yet. The administrator can and may want to activate it himself to be able to send something to that Subscriber.
<h4>Activation and enabling during the mailing process</h4>
<p>When the administrator invokes the mailing of a Newsletter to a mailing List, all the Subscribers from that list are added to a mailing queue including the inactive or disabled Subscribers. However, newsletters will only be sent to the enabled subscribers that are activated for that list. Queued items that have a disabled subscriber or an inactive list status will have a queue status of pended until they are enabled or their subscription to the mailing list becomes confirmed (activated). So we can say that the Administrator can activate a subscriber manually to be able to mail him something, and can disable him to prevent any mailing to him.
<p>Let’s consider an example:
<p>Lets say the administrator invokes the mailing of a newsletter to a list (which starts by sending ALL the subscribers on that list to the mail queue) and there are both inactive and disabled subscribers on that list. The mailer will send the newsletter to all the Enabled and Activated Subscribers and leave the status as Pending for any Subscribers that are Disabled or Inactive. Later, if the administrator enables a disabled subscriber from that mailing list, the mailer will now send a newsletter to that newly enabled subscriber. 
Also, if a subscriber confirms (activates) their subscription to that mailing list, the mailer will now send a newsletter to that newly activated subscriber.
