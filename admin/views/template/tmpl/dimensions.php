        <div id="tab-container-dimensions">
			
			<?php if(in_array('width_column1', $this->columns) || in_array('height_column1', $this->columns)) { ?>
            <div id="dimensions1" class="form-text">
				<dl>
					<dt></dt>
					<dd>
						<?php echo JText::_('COM_NEWSLETTER_COLUMN') . ' 1'; ?>
					</dd>
					<dt>
						<?php 
							if(in_array('width_column1', $this->columns)) {
								echo $this->tplForm->getLabel('width_column1'); 
							}
						?>
					</dt>
					<dd>
						<?php 
							if(in_array('width_column1', $this->columns)) {
								echo $this->tplForm->getInput('width_column1'); 
							}
						?>
					</dd>
					<dt>
						<?php 
							if(in_array('height_column1', $this->columns)) {
								echo $this->tplForm->getLabel('height_column1'); 
							}
						?>
					</dt>
					<dd>
						<?php 
							if(in_array('height_column1', $this->columns)) {
								echo $this->tplForm->getInput('height_column1'); 
							}
						?>
					</dd>
				</dl>
            </div>
			<?php } ?>
			
			<?php if(in_array('width_column2', $this->columns) || in_array('height_column2', $this->columns)) { ?>
            <div id="dimensions2" class="form-text">
				<dl>
					<dt></dt>
					<dd>
						<?php echo JText::_('COM_NEWSLETTER_COLUMN') . ' 2'; ?>
					</dd>
					<dt>
						<?php 
							if(in_array('width_column2', $this->columns)) {
								echo $this->tplForm->getLabel('width_column2'); 
							}
						?>
					</dt>
					<dd>
						<?php 
							if(in_array('width_column2', $this->columns)) {
								echo $this->tplForm->getInput('width_column2'); 
							}
						?>
					</dd>
					<dt>
						<?php 
							if(in_array('height_column2', $this->columns)) {
								echo $this->tplForm->getLabel('height_column2'); 
							}
						?>
					</dt>
					<dd>
						<?php 
							if(in_array('height_column2', $this->columns)) {
								echo $this->tplForm->getInput('height_column2'); 
							}
						?>
					</dd>
				</dl>
            </div>
			<?php } ?>
			
			<?php if(in_array('width_column3', $this->columns) || in_array('height_column3', $this->columns)) { ?>
            <div id="dimensions3" class="form-text">
				<dl>
					<dt></dt>
					<dd>
						<?php echo JText::_('COM_NEWSLETTER_COLUMN') . ' 3'; ?>
					</dd>
					<dt>
						<?php 
							if(in_array('width_column3', $this->columns)) {
								echo $this->tplForm->getLabel('width_column3'); 
							}
						?>
					</dt>
					<dd>
						<?php 
							if(in_array('width_column3', $this->columns)) {
								echo $this->tplForm->getInput('width_column3'); 
							}
						?>
					</dd>
					<dt>
						<?php 
							if(in_array('height_column3', $this->columns)) {
								echo $this->tplForm->getLabel('height_column3'); 
							}
						?>
					</dt>
					<dd>
						<?php 
							if(in_array('height_column3', $this->columns)) {
								echo $this->tplForm->getInput('height_column3'); 
							}
						?>
					</dd>
				</dl>
            </div>
			<?php } ?>
        </div>
