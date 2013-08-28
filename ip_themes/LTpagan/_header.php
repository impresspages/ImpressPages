<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>

    <script type="text/javascript">
        less = {
            env: "development", // or "production"
            async: false,       // load imports async
            fileAsync: true,   // load imports async when in a page under
            // a file protocol
            poll: 1000,         // when in watch mode, time in ms between polls
            functions: {},      // user functions, keyed by name
//            dumpLineNumbers: "mediaQuery", // or "mediaQuery" or "all"
            relativeUrls: true // whether to adjust url's to be relative
            // if false, url's are already relative to the
            // entry less file
            //rootpath: ":/a.com/"// a path to add on to the start of every url
            //resource
        };
    </script>
    <link rel="stylesheet/less" type="text/css" href="<?php echo BASE_URL . THEME_DIR . THEME ?>/less/theme.less" />
    <?php
    // $site->addCss($site->compileThemeLess(THEME, 'theme.less'));
    $site->addCss(BASE_URL.LIBRARY_DIR.'js/colorbox/themes/2/colorbox.css');
    echo $site->generateHead();
    ?>
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script src="<?php echo BASE_URL . THEME_DIR . THEME ?>/less-1.4.2.min.js"></script>
    <script>less.watch();</script>

</head>
<body<?php if ($site->managementState()) { echo ' class="manage"'; } ?> >

<div class="container_12 wrapper">
    <header class="grid_12">
        <?php echo $this->generateManagedLogo(); ?>
        <?php echo $this->generateBlock('ipSitemap'); ?>
        <div class="languages">
            <?php echo $this->generateBlock('ipLanguages'); ?>
        </div>
        <?php echo $this->generateBlock('ipSearch'); ?>
        <?php echo $this->generateManagedImage('homeImage1', THEME_DIR.THEME.'/img/header.jpg', array('width' => '940'), 'banner'); ?>
        <div class="topmenu clearfix">
            <?php
            //first argument is unique name of this menu within your theme. Choose anything you like. Next argument is zone name. They don't have to be equal.
            echo $this->generateMenu('top', 'menu1');
            ?>
        </div>
    </header>
