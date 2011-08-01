        <div id="tab-container-images">

            <div id="images-top1" class="form-text">
            <dl>
                <dt>
                    <?php echo $this->tplForm->getLabel('image_top'); ?>
                </dt>
                <dd>
                    <?php echo $this->tplForm->getInput('image_top'); ?>
                </dd>
                <dt>
                    <?php echo $this->tplForm->getLabel('image_top_alt'); ?>
                </dt>
                <dd>
                    <?php echo $this->tplForm->getInput('image_top_alt'); ?>
                </dd>
            </dl>
            </div>

            <div id="images-top2" class="form-text">
                <dl>
                    <dd>
                        <a
							rel="{handler: 'iframe', size: {x: 700, y: 350}}"
							href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid=jform_image_top&amp;folder=banners"
							class="modal button"
							id="button-image-manager-top"
							onclick=""
							name="image_manager_top">

							<?php echo JText::_('COM_NEWSLETTER_LINK_IMAGE_FOLDER'); ?>
						</a>
                    </dd>
                </dl>
            </div>

            <div id="images-top3" class="form-text">
            <dl>
                <?php echo $this->tplForm->getLabel('image_top_width'); ?>
                <?php echo $this->tplForm->getInput('image_top_width'); ?>
                <?php echo $this->tplForm->getLabel('image_top_height'); ?>
                <?php echo $this->tplForm->getInput('image_top_height'); ?>
            </dl>
            </div>
            <div id="images-top4" class="form-text">
            <dl>
                <dt>
                    <?php echo $this->tplForm->getLabel('image_bottom'); ?>
                </dt>
                <dd>
                    <?php echo $this->tplForm->getInput('image_bottom'); ?>
                </dd>
                <dt>
                    <?php echo $this->tplForm->getLabel('image_bottom_alt'); ?>
                </dt>
                <dd>
                    <?php echo $this->tplForm->getInput('image_bottom_alt'); ?>
                </dd>
            </dl>
            </div>

            <div id="images-top5" class="form-text">
                <dl>
                    <dd>
                        <a
							rel="{handler: 'iframe', size: {x: 700, y: 350}}"
							href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid=jform_image_bottom&amp;folder=banners"
							class="modal button"
							id="button-image-manager-bottom"
							onclick=""
							name="image_manager_bottom">

							<?php echo JText::_('COM_NEWSLETTER_LINK_IMAGE_FOLDER'); ?>
						</a>
                    </dd>
                </dl>
            </div>

            <div id="images-top6" class="form-text">
            <dl>
                <?php echo $this->tplForm->getLabel('image_bottom_width'); ?>
                <?php echo $this->tplForm->getInput('image_bottom_width'); ?>
                <?php echo $this->tplForm->getLabel('image_bottom_height'); ?>
                <?php echo $this->tplForm->getInput('image_bottom_height'); ?>
            </dl>
            </div>
        </div>
