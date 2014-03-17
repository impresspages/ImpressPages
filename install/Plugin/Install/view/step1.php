<h1><?php _e('System check', 'Install') ?></h1>

<table class="table">
<?php foreach ($requirements as $row) { ?>
<?php
    $typeLabel = $class = '';
    switch ($row['type']) {
        case 'success':
            $typeLabel = __('Ok', 'Install');
            $class = 'success';
            break;
        case 'warning':
            $typeLabel = __('Warning', 'Install');
            $class = 'warning';
            break;
        case 'error':
            $typeLabel = __('Error', 'Install');
            $class = 'danger';
            break;
    }
    ?>
    <tr><th><?php echo $row['name'] /*Do not escape. HTML is used*/ ?></th><td class="text-center <?php echo esc($class, 'attr') ?>"><?php echo esc($typeLabel) ?></td></tr>
<?php } ?>
</table>
<p class="text-right">
<?php if ($errors) { ?>
    <a class="btn btn-primary" href="?step=1"><?php _e('Check again', 'Install') ?></a>
<?php } else { ?>
    <a class="btn btn-default" href="?step=1"><?php _e('Check again', 'Install') ?></a>
    <a class="btn btn-primary ipsStep1" href="?step=2"><?php _e('Next', 'Install') ?></a>
<?php } ?>
</p>
