<h1><?php echo IP_STEP_DB_LONG ?></h1>

<div id="errorAllFields" class="noDisplay"><p class="error"><?php echo IP_DB_ERROR_ALL_FIELDS ?></p></div>
<div id="errorConnect" class="noDisplay"><p class="error"><?php echo IP_DB_ERROR_CONNECT ?></p></div>
<div id="errorDb" class="noDisplay"><p class="error"><?php echo IP_DB_ERROR_DB ?></p></div>
<div id="errorQuery" class="noDisplay"><p class="error"><?php echo IP_DB_ERROR_QUERY ?></p></div>
<div id="errorLongPrefix" class="noDisplay"><p class="error"><?php echo IP_DB_ERROR_LONG_PREFIX ?></p></div>
<div id="errorIncorrectPrefix" class="noDisplay"><p class="error"><?php echo IP_DB_ERROR_INCORRECT_PREFIX ?></p></div>
<form onsubmit="return false">
    <p><strong><?php echo IP_DB_SERVER ?></strong><input id="db_server" type="text" name="server" value="<?php echo $_SESSION['db_server'] ?>"></p>
    <p><strong><?php echo IP_DB_USER ?></strong><input id="db_user" type="text" name="db_user" value="<?php echo $_SESSION['db_user'] ?>"></p>
    <p><strong><?php echo IP_DB_PASS ?></strong><input id="db_pass" type="password" name="db_pass" value="<?php echo $_SESSION['db_pass'] ?>"></p>
    <p><strong><?php echo IP_DB_DB ?></strong><input id="db_db" type="text" name="db" value="<?php echo $_SESSION['db_db'] ?>"></p>
    <p><strong><?php echo IP_DB_PREFIX ?></strong><input id="db_prefix" maxlength="7" type="text" name="prefix" value="<?php echo $_SESSION['db_prefix'] ?>"></p>

</form>
<p><?php echo IP_DB_DATA_WARNING ?></p>
<a class="button_act" href="#"><?php echo IP_NEXT ?></a>
<a class="button" href="?step=2"><?php echo IP_BACK ?></a>