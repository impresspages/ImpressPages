<script>
var ip = {
    baseUrl : <?php echo json_encode($ipBaseUrl) ?>,
    languageId : <?php echo json_encode($ipLanguageId) ?>,
    languageUrl : <?php echo json_encode($ipLanguageUrl) ?>,
    theme : <?php echo json_encode($ipTheme) ?>,
    zoneName : <?php echo json_encode($ipZoneName) ?>,
    pageId : <?php echo json_encode($ipPageId) ?>,
    revisionId : <?php echo json_encode($ipRevisionId) ?>,
    securityToken : <?php echo json_encode($ipSecurityToken) ?>,
    developmentEnvironment : <?php echo json_encode($ipDevelopmentEnvironment) ?>,
    debugMode : <?php echo json_encode($ipDebugMode) ?>
};
</script>
<script>
<?php foreach ($javascriptVariables as $name => $variable) { ?>
    var <?php echo $name ?> = <?php echo json_encode($variable); ?>;
<?php } ?>
</script>
<?php
    foreach ($javascript as $levelKey => $level) {
        foreach ($level as $recordKey => $record) {
            if ($record['type'] == 'file') {
                echo '  <script type="text/javascript" src="' . $record['value'] . '"';
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
   }
