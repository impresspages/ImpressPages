        <title><?php echo htmlspecialchars($title) ?></title>
        <link rel="shortcut icon" href="<?php echo htmlspecialchars($favicon) ?>" />
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
        <meta name="keywords" content="<?php echo htmlspecialchars($keywords) ?>" />
        <meta name="description" content="<?php echo htmlspecialchars($description) ?>" />
        <meta name="generator" content="ImpressPages CMS 1.1 under GNU GPL license" />
<?php foreach ($css as $key => $file) { ?>
        <link href="<?php echo $file ?>" rel="stylesheet" type="text/css" />
<?php } ?>

