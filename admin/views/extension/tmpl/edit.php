<?php
// no direct access
defined('_JEXEC') or die;
?>

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

			<form name="extensionForm" method="POST" id="extension-container" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_newsletter'); ?>">

				<div id="ext-settings-container">

					<ul class="adminformlist">

						<li><?php echo $this->form->getLabel('title'); ?>
						<?php echo $this->form->getInput('title'); ?></li>

						<li><?php echo $this->form->getLabel('showtitle'); ?>
						<?php echo $this->form->getInput('showtitle'); ?></li>
					</ul>

					<div class="clr"></div>

					<?php echo JHtml::_('sliders.start', 'module-sliders'); ?>

					<?php
					$fieldSets = $this->form->getFieldsets('params');

					foreach ($fieldSets as $name => $fieldSet) :
						$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MODULES_'.$name.'_FIELDSET_LABEL';
						echo JHtml::_('sliders.panel',JText::_($label), $name.'-options');
							if (isset($fieldSet->description) && trim($fieldSet->description)) :
								echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
							endif;
							?>
						<fieldset class="panelform">
						<?php $hidden_fields = ''; ?>
						<ul class="adminformlist">
							<?php foreach ($this->form->getFieldset($name) as $field) : ?>
							<?php if (!$field->hidden) : ?>
							<li>
								<?php echo $field->label; ?>
								<?php echo $field->input; ?>
							</li>
							<?php else : $hidden_fields.= $field->input; ?>
							<?php endif; ?>
							<?php endforeach; ?>
						</ul>
						<?php echo $hidden_fields; ?>
						</fieldset>
					<?php endforeach; ?>

					<?php echo JHtml::_('sliders.end'); ?>
				</div>
				<input
					class="btn btn-success extension-save"
					type="button"
					value="<?php echo JText::_('JTOOLBAR_APPLY'); ?>"
					onclick="return Joomla.submitbutton('apply');"
				>
			</form>

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