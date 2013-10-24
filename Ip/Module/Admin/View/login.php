<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ImpressPages</title>
    <link rel="stylesheet" href="<?php echo BASE_URL . CORE_DIR ?>Ip/Backend/design/login/login.css">
    <link rel="stylesheet" href="<?php echo BASE_URL . LIBRARY_DIR ?>css/bootstrap/bootstrap.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL ?>'favicon.ico">
</head>
<body>


<a href="http://www.impresspages.org/" class="logo" target="_blank"><img src="<?php echo BASE_URL . CORE_DIR ?>Ip/Backend/design/login/logo.png"></a>
<div class="verticalAlign"></div>
<div class="login">
    <div class="loginTitle">
        <h1>Login</h1>
    </div>
    <?php echo $loginForm->render() ?>
</div>
<div class="loginFooter">Copyright 2009-<?php echo date("Y") ?> by <a href="http://www.impresspages.org/">ImpressPages UAB</a></div>
<?php echo $site->generateJavascript(); ?>
</body>
</html>