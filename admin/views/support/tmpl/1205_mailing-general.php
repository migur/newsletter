<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="content">
<h1>Send Newsletter - Help page</h1>
<br/><br/>
<h3>Understanding the flow</h3>
<p>The component has the layer that handles almost all mailings (except immediate 
mailing on subscription via Subscription Module). 
The layer uses mailing queue to send letters out. 
All triggers (sending newsletter to a list(s), automailing, etc.) 
use this layer and just push into it (add to queue) the information of which the 
newsletter and for whom need to mail it. To send mails from the queue out you 
need a CRON job or you can use <b>Dashboard -> Process queue</b>.

<h3>Mail duplication handling</h3>
<p>You can send the same newsletter to several lists (more than one). 
One subscriber may be assigned to several lists. 
Let’s assume that we have 2 lists(L1 and L2) and subscriber S1 that assigned 
to both L1 and L2. If you will send N1 newsletter to both lists (L1 and L2) 
then S1 subscriber will receive the N1 only once. In this way the component 
avoids the mail duplication. You are able to send N1 to S1 only once. 
No matter to which lists S1 belongs and to which lists N1 will be sent.
If you still want to duplicate it then just do a copy of N1 via 
<b>Newsletter page -> Save as Copy</b> and send N1 to L1 and copy of N1 to L2.
</div>
