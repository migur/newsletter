<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="content">
<h1>The Extension Manager - Help page</h1>
<br/><br/>
This is a tool to manage add-ons for Migur Newsletter component.
With this tool you can:
<ul>
	<li>Add new extension</li>
    <li>Remove extension</li>
    <li>View the list of installed extensions</li>
    <li>Find and repair the extensions that exist on file system but not registered</li>
</ul>

<h3>TYPES OF EXTENSIONS</h3>
<p>At this point we support 3 types of extensions:</p>
<ul>
	<li>Module</li>
    <li>Plugin</li>
    <li>Template</li>
</ul>

<p><b>Templates</b> are intended to provide the skeleton for your newsletter. But the templates itself are 
just frames. To use it in newsletter you need to customize it (create a "instance") on Templates page.</p>

<p><b>Modules</b> are the information blocks for newsleter. They can be added into newsletter for displaying some information.
It are similar to J! native modules. Modules can be placed into a special areas of a template ("module positions").</p>

<p><b>Plugins</b> are kind of most common extensions it can be used in newsletter (Goolge Analytics plugin) for importing of subscribers into component or....</p>

<h3>ADD AND VIEW THE EXTENSION</h3>
<p>Just provide the path to the extension zip-package and click to "Upload and install" button.
If all gone ok you will see this one in the list. After it you can use the extension.

<h3>REMOVE THE EXTENSION</h3>
<p>Pick the extension(s) that you want to remove and click "Delete" toolbar button.
If all gone ok you will see this one in the list. After it you can use the extension.
<span style="color:red">When you delete the extension you loose all data associated with it.</span>
For example: if you have Article module in several newsletters then all the instances of this module 
will dissapear with all his data from these newsletters when you delete it.</p>

<h3>EXAMPLES AND SOURCES</h3>
	<ul>
		<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/extension/modules/mod_article.zip'?>">Article module</a></li>
		<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/extension/modules/mod_rss.zip'?>">RSS module</a></li>
		<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/extension/modules/mod_img.zip'?>">Image module</a></li>
		<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/extension/modules/mod_text.zip'?>">Text module</a></li>
		<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/extension/modules/mod_wysiwyg.zip'?>">WYSIWYG module</a></li>
		<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/extension/templates/singlecolumn1.zip'?>">Singlecolumn template</a></li>
		<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/extension/templates/doublecolumn1.zip'?>">Doublecolumn template</a></li>
		<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/extension/templates/threecolumn1.zip'?>">Threecolumn template</a></li>
		<li><a href="<?php echo JUri::root() . 'media/com_newsletter/examples/extension/plugins/ganalytics.zip'?>">Google Analytics plugin</a></li>
	</ul>	
</div>
