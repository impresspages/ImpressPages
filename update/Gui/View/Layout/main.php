<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo $this->escTran('global_title') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <link href="public/style.css" rel="stylesheet" type="text/css" />
    <link rel="SHORTCUT ICON" href="favicon.ico" />
</head>
<body>
    <div class="container">
        <img id="logo" src="public/cms_logo.png" alt="ImpressPages CMS" />
        <div class="clear"></div>
        <div id="wrapper">
            <img class="border" src="public/cms_main_top.gif" alt="Design" />
            <div id="main">
                <div id="content">
                    <?php echo $content ?>
                </div>
                <div class="clear"></div>
            </div>
            <img class="border" src="public/cms_main_bottom.gif" alt="Design" />
            <div class="clear"></div>
        </div>
        <div class="footer">
            Copyright 2009-<?php echo date("Y") ?> by <a href="http://www.impresspages.org">ImpressPages UAB</a>
        </div>
    </div>
    <div class="noDisplay">
        <div class="loading">
        </div>
    </div>
    <script src="public/jquery.min.js"></script>
    <script src="public/script.js"></script>
</body>
</html>