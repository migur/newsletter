<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$sess = JFactory::getSession();
$data = $sess->get('com_newsletter.uploader.file');
$sess->clear('com_newsletter.uploader.file');

$msg = JRequest::getString('message');

$params = JRequest::getVar('params', array(), 'default', 'array');

$callback = !empty($params['callback'])? $params['callback'] : 'OnUploadCompleteCallback';

?>

<?php if ($msg): ?>
<div class="alert alert-<?php echo $data['status']? 'message' : 'error'?>">
	<span><?php echo $msg; ?></span>
</div>
<?php endif; ?>

<!-- File Upload Form -->
<form action="<?php echo JRoute::_('index.php', false); ?>" class="form-inline" name="uploadForm" method="post" enctype="multipart/form-data">
	<div id="uploadform">
		
		<div id="upload-noflash" class="actions">
			<div class="pull-left">
				<label for="upload-file" class="control-label"><?php echo JText::_('COM_NEWSLETTER_UPLOAD_FILE'); ?></label>
				<input type="file" id="upload-file" name="Filedata[]" multiple /> 
				<button class="btn btn-primary" id="upload-submit">
					<i class="icon-upload icon-white"></i> <?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?>
				</button>
			</div>	
			<div style="margin:6px 0 0 10px;" class="pull-left preloader-container"></div>
			<br/>
			<br/>
			<span class="label label-info"><?php echo $this->config->get('upload_maxsize') == '0' ? JText::_('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></span>
			<br/>
			<br/>
		</div>
		
		<div id="upload-flash" class="hide">
			<div class="btn-toolbar">
				<div class="btn-group"><a class="btn" href="#" id="upload-browse"><i class="icon-folder"></i> <?php echo JText::_('COM_MEDIA_BROWSE_FILES'); ?></a><a class="btn" href="#" id="upload-clear"><i class="icon-remove"></i> <?php echo JText::_('COM_MEDIA_CLEAR_LIST'); ?></a></div>
				<div class="btn-group"><a class="btn btn-primary" href="#" id="upload-start"><i class="icon-upload icon-white"></i> <?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?></a></div>
			</div>
			<div class="clearfix"></div>
			<p class="overall-title"></p>
			<div class="overall-progress"></div>
			<div class="clearfix"></div>
			<p class="current-title"></p>
			<div class="current-progress"></div>
			<p class="current-text"></p>
		</div>
		<ul class="upload-queue list-striped list-condensed" id="upload-queue">
			<li style="display:none;"></li>
		</ul>
		
		<input type="hidden" name="option" value="com_newsletter" />
		
		<?php foreach($params as $name => $val): ?>
			<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $val; ?>" />
		<?php endforeach; ?>	
			
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


<script type="text/javascript">

	(function(){
		
		var data = <?php echo json_encode($data); ?>;
		
		if (data && parent && parent.window && parent.window.<?php echo $callback; ?>) {
			parent.window.<?php echo $callback; ?>(data);
		}

	})();

</script>
