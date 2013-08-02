<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="content">
	<h1>List to J! group bindings - Help page</h1>
	<br/><br/>
<p>Admin can create relations between component’s list and one or more J! group. 
The idea is to create rules under which the synchronisation between list and group will be performed. 
Each rule is based on one event:
<ul>
	<li>on add user to system</li>
	<li>on add user to group</li>
	<li>on remove user from group</li>
</ul>
and group for which rule is created. Also admin must select what to do when the event will occured for this group. 
Two actions are supported:
<ul>
	<li>add to list</li>
	<li>remove from list.</li>
</ul>

<p>As instance, rule may looks like this
 <b>On add user to group “Authors” add to “Writters” list</b>. 
<p>To create this rule admin need to go to List ->Advanced->”New” 
button (below the table) and select event type, group and action.
	
<p>This functionality covers all actions of J! user manager:
<ul>
	<li>adding user to J! groups on creating of a user</li>
	<li>changing user’s groups membership on editing of a user</li>
	<li>removing user</li>
	<li>batch processing of users (add, remove, set a group). <b>Note that processing of big amount of users may take a lot of time.</b></li>
</ul>
<p>Since this functionality is implemented on basis of a J! plugin then it will work in other cases that 
trigger onUserSaveBefore and onUserSaveAfter events.	
</div>

