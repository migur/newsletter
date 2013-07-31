<?php $items = $this->form->getFieldset('newsletters'); ?>

<?php foreach ($items as $i => $item) : ?>
	<div class="control-group">
		<div class="control-label">
				<?php echo $item->label; ?>
		</div>
		<div class="controls offset4">
			<?php echo $item->input; ?>
		</div>	
	</div>
<?php endforeach; ?>
