<?php if (!defined('CMS')) exit; ?>
<?php
// TODOX: delete this file, it's for the grid testing only
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php $site->addCss(BASE_URL.LIBRARY_DIR.'js/colorbox/themes/2/colorbox.css'); ?>
    <?php $site->addCss(BASE_URL.THEME_DIR.THEME.'/ip_content.css'); ?>
    <?php $site->addCss(BASE_URL.THEME_DIR.THEME.'/theme.css'); ?>
    <?php echo $site->generateHead(); ?>
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body<?php if ($site->managementState()) { echo ' class="manage"'; } ?> >

<div class="theme showGridHint">
    <div class="gridHint">
        <div class="col_1"></div>
        <div class="col_1"></div>
        <div class="col_1"></div>
        <div class="col_1"></div>
        <div class="col_1"></div>
        <div class="col_1"></div>
        <div class="col_1"></div>
        <div class="col_1"></div>
        <div class="col_1"></div>
        <div class="col_1"></div>
        <div class="col_1"></div>
        <div class="col_1"></div>
    </div>
    <div class="grid clearfix">
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
        <div class="col_1">1 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_2">2 column</div>
        <div class="col_2">2 column</div>
        <div class="col_2">2 column</div>
        <div class="col_2">2 column</div>
        <div class="col_2">2 column</div>
        <div class="col_2">2 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_3">3 column</div>
        <div class="col_3">3 column</div>
        <div class="col_3">3 column</div>
        <div class="col_3">3 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_4">4 column</div>
        <div class="col_4">4 column</div>
        <div class="col_4">4 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_5">5 column</div>
        <div class="col_5">5 column</div>
        <div class="col_2">2 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_6">6 column</div>
        <div class="col_6">6 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_7">7 column</div>
        <div class="col_5">5 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_8">8 column</div>
        <div class="col_4">4 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_9">9 column</div>
        <div class="col_3">3 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_10">10 column</div>
        <div class="col_2">2 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_11">11 column</div>
        <div class="col_1">1 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_12">
            12 column
            <div class="grid clearfix">
                <div class="col_11">11 column</div>
                <div class="col_1">1 column</div>
            </div>
        </div>
    </div>
    <div class="grid clearfix">
        <div class="col_3">3 column</div>
        <div class="col_3">3 column</div>
        <div class="col_6">6 column</div>
    </div>
    <div class="grid clearfix">
        <div class="col_6">
            6 column
            <div class="grid clearfix">
                <div class="col_6">
                    6 column
                    <div class="grid clearfix">
                        <div class="col_2">2 column</div>
                        <div class="col_4">4 column</div>
                    </div>
                </div>
                <div class="col_6">6 column</div>
            </div>
        </div>
        <div class="col_6">
            6 column
            <div class="grid clearfix">
                <div class="col_4">4 column</div>
                <div class="col_2">2 column</div>
                <div class="col_4">4 column</div>
                <div class="col_4">4 column</div>
            </div>
        </div>
    </div>
    <div class="grid clearfix">
        <div class="col_3">
            3 column
            <div class="grid clearfix">
                <div class="col_2">2 column</div>
                <div class="col_1">1 column</div>
                <div class="col_3">3 column</div>
            </div>
        </div>
        <div class="col_8">
            8 column
            <div class="grid clearfix">
                <div class="col_2">2 column</div>
                <div class="col_2">2 column</div>
                <div class="col_2">2 column</div>
                <div class="col_2">2 column</div>
            </div>
        </div>
    </div>
    <div class="grid clearfix">
        <div class="col_1">
            1 column
            <div class="grid clearfix">
                <div class="col_6">6 column</div>
                <div class="col_1">1 column</div>
                <div class="col_12">12 column</div>
            </div>
        </div>
    </div>
    <div class="grid clearfix">
        <div class="col_12 col_sm_4">
            12 / 4sm column
            <div class="grid clearfix">
                <div class="col_6 col_sm_2">6 / 2sm column</div>
                <div class="col_6 col_sm_2">6 / 2sm column</div>
            </div>
        </div>
    </div>
</div>
<?php
    $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
    $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/colorbox/jquery.colorbox.js');
    $site->addJavascript(BASE_URL.THEME_DIR.THEME.'/site.js');
    echo $site->generateJavascript();
?>
</body>
</html>
