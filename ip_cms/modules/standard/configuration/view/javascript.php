<script>
var ip = {
    baseUrl : <?php echo json_encode($ipBaseUrl) ?>,
    languageUrl : <?php echo json_encode($ipLanguageUrl) ?>,
    libraryDir : <?php echo json_encode($ipLibraryDir) ?>,
    themeDir : <?php echo json_encode($ipThemeDir) ?>,
    pluginDir : <?php echo json_encode($ipPluginDir) ?>,
    moduleDir : <?php echo json_encode($ipModuleDir) ?>,
    theme : <?php echo json_encode($ipTheme) ?>,
    zoneName : <?php echo json_encode($ipZoneName) ?>,
    pageId : <?php echo json_encode($ipPageId) ?>,
    revisionId : <?php echo json_encode($ipRevisionId) ?>
};
</script>
<script>
<?php foreach ($javascriptVariables as $name => $variable) { ?>
    var <?php echo $name ?> = <?php echo json_encode($variable); ?>;
<?php } ?>
</script>
<?php foreach ($javascript as $levelKey => $level) { ?>
    <?php foreach ($level as $recordKey => $record) { ?>
        <?php if ($record['type'] == 'file') { ?>
            <script type="text/javascript" src="<?php echo $record['value'] ?>"></script>
        <?php } ?>
        <?php if ($record['type'] == 'variable') { ?>
            <script type="text/javascript">
                var <?php echo $recordKey; ?> = <?php echo json_encode($record['value']); ?>;
            </script>
        <?php } ?>
        <?php if ($record['type'] == 'content') { ?>
            <script type="text/javascript">
        <?php echo $record['value']; ?>
            </script>
        <?php } ?>
    <?php } ?>
<?php } ?>
