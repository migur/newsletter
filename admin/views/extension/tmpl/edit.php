<?php
// no direct access
defined('_JEXEC') or die;
?>

<form name="extensionForm" method="POST" id="extension-container" class="form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">
<div id="extensions">

	<ul id="tabs-extensions" class="nav nav-tabs">
		<li class="active">
			<a data-toggle="tab" href="#ext-settings"><?php echo JText::_('COM_NEWSLETTER_EXTENSION_SETTINGS'); ?></a>
		</li>	
		<li>
			<a data-toggle="tab" href="#ext-info"><?php echo JText::_('COM_NEWSLETTER_EXTENSION_INFORMATION'); ?></a>
		</li>	
	</ul>

	<div class="tab-content">

		<div id="ext-settings" class="tab-pane active">


				<div id="ext-settings-container">

					<div class="control-group adminformlist">
						<div class="control-label">
							<?php echo $this->form->getLabel('title'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('title'); ?>
						</div>	
					</div>
					
					<div class="control-group adminformlist">
						<div class="control-label">
							<?php echo $this->form->getLabel('showtitle'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('showtitle'); ?>
						</div>	
					</div>
					
					
					<div class="accordion pane-sliders" id="module-sliders">

					<?php
					$fieldSets = $this->form->getFieldsets('params');

					foreach ($fieldSets as $name => $fieldSet) : 
						$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MODULES_'.$name.'_FIELDSET_LABEL';	
						$id = $name . '-options'; ?>
						
						<div class="accordion-group panel">
							<div class="accordion-heading">
								<a class="accordion-toggle" data-toggle="collapse" data-parent="#module-sliders" href="#<?php echo $id; ?>">
									<?php echo JText::_($label); ?>
								</a>
							</div>

							<div id="<?php echo $id; ?>" class="accordion-body collapse in" >
								<div class="accordion-inner">
									<?php 
									$hidden_fields = '';
									$fieldset = $this->form->getFieldset($name);
									foreach ($fieldset as $field) :
										if ($field->hidden) {
											$hidden_fields.= $field->input;
										} else { ?>
											<div class="control-group adminformlist">
												<div class="control-label">
													<?php echo $field->label; ?>
												</div>
												<div class="controls">
													<?php echo $field->input; ?>
												</div>	
											</div>
										<?php } ?>
									<?php endforeach; ?>
									<?php echo $hidden_fields; ?>
								</div>	
							</div>
						</div>	
					<?php endforeach; ?>
					</div>
				</div>
				<input
					class="btn btn-success extension-save"
					type="button"
					value="<?php echo JText::_('JTOOLBAR_APPLY'); ?>"
					onclick="return Joomla.submitbutton('apply');"
				>

		</div>	

		<div id="ext-info" class="tab-pane">

			<table class="sslist adminlist  table table-striped">
				<tbody>
				<?php $idx = 0; foreach ($this->info as $i => $item) : ?>
					<tr class="row<?php echo $idx % 2; ?>">
						<td>
							<?php echo $this->escape($i); ?>
						</td>
						<td>
							<?php echo $this->escape($item); ?>
						</td>
					</tr>
				<?php $idx++; endforeach; ?>
				</tbody>
			</table>

		</div>	

	</div>	

</div>
</form>
	