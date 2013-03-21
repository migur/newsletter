<?php
// no direct access
defined('_JEXEC') or die;
?>

<table class="table table-striped" width="100%">
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
