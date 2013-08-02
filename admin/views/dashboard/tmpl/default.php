<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="container" id="dashboard-handlers">
    <?php echo $this->loadTemplate('handlers', ''); ?>
</div>

<div class="container" id="dashboard-static">
    <?php echo $this->loadTemplate('static', ''); ?>
</div>