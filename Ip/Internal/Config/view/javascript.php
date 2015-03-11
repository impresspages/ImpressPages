<script>
var ip = <?php echo json_encode($ip) ?>;
<?php foreach ($javascriptVariables as $name => $variable) { ?>
    var <?php echo $name ?> = <?php echo json_encode($variable); ?>;
<?php } ?>
</script>
<?php
foreach ($javascript as $recordKey => $record) {
    if ($record['type'] == 'file') {
        echo '  <script type="text/javascript" src="' . escAttr($record['value']) . '"';
        if (is_array($record['attributes'])) {
            echo join(
                ' ',
                array_map(
                    function ($sKey, $sValue) {
                        return esc($sKey) . '="' . esc($sValue) . '"';
                    },
                    array_keys($record['attributes']),
                    array_values($record['attributes'])
                )
            );
        }
        echo '></script>' . "\n";

    }

    if ($record['type'] == 'variable') {
        echo '  <script type="text/javascript">
var ' . $recordKey . ' = ' . json_encode($record['value']) . ';
</script>' . "\n";
    }
    if ($record['type'] == 'content') {
        echo '  <script type="text/javascript">
' . $record['value'] . '
</script>';
    }

}

