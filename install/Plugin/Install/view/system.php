<h1><?php _e('System check', 'Install'); ?></h1>

<table class="table ipsSystemCheck">
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
        <tr><th><?php echo $row['name'] . (!empty($row['helpUrl']) ? ' <a href="' . $row['helpUrl'] . '" target="_blank">(?)</a>' : '') /*Do not escape. HTML is used*/ ?></th><td class="text-center <?php echo escAttr($class); ?>"><?php echo esc($typeLabel); ?></td></tr>
    <?php } ?>
</table>
<p class="text-right">
    <?php if ($showNextStep) { ?>
        <a class="btn btn-primary ipsAutoForward" href="?step=3"><?php _e('Next', 'Install'); ?></a>
    <?php } else { ?>
        <a class="btn btn-primary" href="?step=2"><?php _e('Check again', 'Install'); ?></a>
    <?php } ?>
</p>
