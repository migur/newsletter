<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="content">
<h1>Importing of a CSV file into list - Help page</h1>
<br/><br/>
<p>If you have issues with the CSV upload, try uploading your CSV as a .txt file.
File must has a header row with column names 'Email, Name, Html' with the same quotation and separators as other ones.
You can see these names in 'Fields identified in the imported file' section after success upload.

<p>Import performs in three steps:
<ul>
	<li>Uploading of a CSV file</li>
	<li>Specifying of found columns to Name, Email and Html (type of a newsletter user prefer to receive)</li>
	<li>Importing.</li>
</ul>
<p>Component uses first row of a csv file to detect the amount and specification of a columns.
So after success uploading you can see the items of a first column in 
<b>"Fields identified in the imported file"</b> fieldset.
Then you need to tell to component what column means what by dragging each field
into proper container on <b>Drag fields from the left list into the fields below</b> fieldset.

<p>If you get success(green) message after upload but still cant see the content of
a first row (or see not all fields) in <b>"Fields identified in the imported file"</b> fieldset
then probably you need to play with <b>Settings</b> setting the proper delimiter and enclosure.

<p>Also here you can specify if your file contain usefull data in the first row 
or it just header with column names. Use <b>"Skip the header"</b> setting to do it.
<br/>
<p>If you want to sent the registration mail to these subscribers you need to check 
 the <b>Send registration mail</b> option in <b>Settings</b> on Import panel before starting of importing.
See <a href="<?php echo NewsletterHelperSupport::getResourceUrl('subscriber', 'subscription', 'regmail'); ?>">Subscription</a> for additional info.
 
<h3>Examples of CSVs for import:</h3>
<ul>
	<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/list/import/example1.csv'?>">Example 1</a><br/>
Use "," as delimiter and NO enclosures.<br/></li>

<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/list/import/example2.txt'?>">Example 2</a><br/>
Use TAB as delimiter and " as enclosure.<br/></li>
</ul>
</div>
