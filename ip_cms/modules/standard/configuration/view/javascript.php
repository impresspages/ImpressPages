
<script type="text/javascript">
    var ip = {
        baseUrl : <?php echo json_encode($ipBaseUrl) ?>,
        libraryDir : <?php echo json_encode($ipLibraryDir) ?>,
        themeDir : <?php echo json_encode($ipThemeDir) ?>,
        moduleDir : <?php echo json_encode($ipModuleDir) ?>,
        languageCode :  <?php echo json_encode($ipLanguageCode) ?>,
        theme : <?php echo json_encode($ipTheme) ?>,
        zoneName : <?php echo json_encode($ipZoneName) ?>,
        pageId : <?php echo json_encode($ipPageId) ?>,
        revisionId : <?php echo json_encode($ipRevisionId) ?>
    };
</script>
<?php foreach ($javascript as $key => $file) { ?>
<script type="text/javascript" src="<?php echo $file ?>"></script>
<?php } ?>
