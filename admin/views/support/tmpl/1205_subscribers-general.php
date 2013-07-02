<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="content">
<h1>Subscribers - Help page</h1>
<br/><br/>
    <p>The Administrator manages Subscribers and subscription Lists on the Subscribers page. There you find all the Subscribers and the Lists that the Subscribers can be associated with.
Subscribers are the main target of the component.
There are two types of Subscribers: (represented by different icons)
<ul><li>Joomla! user</li>
<li>Migur subscriber</li></ul>
<b>Joomla! user</b> is the standard Joomla! user that is managed via Joomla! User Manager. All Joomla! users are automatically listed in the list of Subscribers. Disabling a Joomla! user in Migur does not disable them in Joomla! and disabling them in Joomla! does not disable them in Migur.
<b>Migur subscribers</b> are added to the Migur Subscription list through subscription registration modules, imported lists or manually adding a subscriber.
    <p>The definition key of a Subscriber is his email address.
All users are automatically Enabled by the system. Activation on a list can be set to autoconfirmation or require the Subscriber to confirm their request to a list via email verification. Also administrator can activate the Subscriber manually for particular list.
<h4>Toolbar</h4>
<br/><b>Remove from list</b> button removes the selected Subscriber(s) from the List(s) selected in "Lists" panel. At least one Subscriber and one List must be checked.
<br/><b>Add to list</b> button adds the selected Subscriber(s) to the List(s) selected in "Lists" panel. At least one Subscriber and one List must be checked.
<br/><b>New</b> button allows you to create new Migur subscriber filling its name and email. To create Joomla! user please use Joomla! user manager. They will automatically be added to this list.
<br/><b>Delete</b> button allows you to delete Migur subscriber. To delete Joomla! user please use Joomla! user manager.
<br/><b>Enable</b> button allows you to enable a subscriber/Joomla! user. See below.
<br/><b>Disable</b> button allows you to disable a subscriber/Joomla! user. See below.
<br/><b>Activate</b> button appears only if you are viewing subscribers of a particular list (the particular list is selected in "List" filter). This button allows you to activate the subscription of the Subscriber to that selected List manually.
<h4>Subscriber enabling/disabling</h4>
    <p>This feature is similar (but not connected) to enabling/disabling feature in Joomla! user manager. The disabling of a Subscriber means that no emails will be sent to him from any list he is subscribed or added to by admin. It has nothing to do with disabling Joomla! user. All the subscribers are enabled by default. Administrator need to be able to disable subscribers for some reason (see Activation and disabling in mailing process).
<h4>Subscriber activation</h4>
    <p>Activation is the process of confirming a Subscriber’s subscription to particular list. The Subscriber becomes activated for a particular List when he:
<ul><li>confirm a subscription via email</li>
<li>subscribed to a list with autoconfirmation</li>
<li>the administrator can also activate the Subscriber manually if necessary</li>
</ul>

It is similar to the activation feature in the Joomla! User Manager. However Migur activation is not related to the whole Joomla! system but only to a particular mailing List in Migur. This is the main difference. A Subscriber does not confirm himself or his email, he confirms (activates) his subscription to a list. It has nothing to do with the activation of a Joomla! user. It does not matter if the Joomla! user is active or not. Only the activation of a subscriber to a particular List. Inactive status shows us that the Subscriber registered for a subscription but has not approved it via email yet. The administrator can and may want to activate it himself to be able to send something to that Subscriber.
<h4>Activation and enabling during the mailing process</h4>
    <p>When the administrator invokes the mailing of a Newsletter to a List, all the Subscribers from that list are added to a mailing queue including the inactive or disabled Subscribers. However, newsletters will only be sent to the enabled subscribers that are activated for that list. Queued items that are disabled or inactive subscribers will have a status of pended until they are enabled or their subscription to the list becomes confirmed (activated). So we can say that the Administrator can activate a subscriber manually to be able to mail him something, and can disable him to prevent any mailing to him.
    <p>Let’s consider an example:
    <p>Lets say the administrator invokes the mailing of a newsletter to a list (which starts by sending ALL the subscribers on that list to the mail queue) and that list has both inactive and disabled subscribers on it. The mailer will send the newsletter to all the Enabled and Activated Subscribers and leave the status of the others as Pending.
Later, if the administrator enables a disabled subscriber from that list, the mailer will now send a newsletter to that newly enabled subscriber.
Also, if a subscriber confirms (activates) their subscription to that list, the mailer will now send a newsletter to that newly activated subscriber.
