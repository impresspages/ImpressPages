<?PHP


// Please type in all needed values before run the script

    require_once("hn_captcha.class.x1.php");

    // ConfigArray
    $CAPTCHA_INIT = array(

            // string: absolute path (with trailing slash!) to a php-writeable tempfolder which is also accessible via HTTP!
            'tempfolder'     => $_SERVER['DOCUMENT_ROOT'].'/_tmp/',

            // string: absolute path (in filesystem, with trailing slash!) to folder which contain your TrueType-Fontfiles.
            'TTF_folder'     => dirname(__FILE__).'/fonts/',

            // mixed (array or string): basename(s) of TrueType-Fontfiles, OR the string 'AUTO'. AUTO scanns the TTF_folder for files ending with '.ttf' and include them in an Array.
            // Attention, the names have to be written casesensitive!
            //'TTF_RANGE'    => 'NewRoman.ttf',
            //'TTF_RANGE'    => 'AUTO',
            //'TTF_RANGE'    => array('actionj.ttf','bboron.ttf','epilog.ttf','fresnel.ttf','lexo.ttf','tetanus.ttf','thisprty.ttf','tomnr.ttf'),
            'TTF_RANGE'    => 'AUTO',

            'chars'          => 5,       // integer: number of chars to use for ID
            'minsize'        => 20,      // integer: minimal size of chars
            'maxsize'        => 30,      // integer: maximal size of chars
            'maxrotation'    => 25,      // integer: define the maximal angle for char-rotation, good results are between 0 and 30

            'noise'          => TRUE,    // boolean: TRUE = noisy chars | FALSE = grid
            'websafecolors'  => FALSE,   // boolean
            'refreshlink'    => TRUE,    // boolean
            'lang'           => 'en',    // string:  ['en'|'de']
            'maxtry'         => 3,       // integer: [1-9]

            'badguys_url'    => '/',     // string: URL
            'secretstring'   => 'A very, very secret string which is used to generate a md5-key!',
            'secretposition' => 24,      // integer: [1-32]

            'counter_filename'        => '',              // string: absolute filename for textfile which stores current counter-value. Needs read- & write-access!
            'prefix'                => 'hn_captcha_',   // string: prefix for the captcha-images, is needed to identify the files in shared tempfolders
            'collect_garbage_after'    => 20,             // integer: the garbage-collector run once after this number of script-calls
            'maxlifetime'            => 60              // integer: only imagefiles which are older than this amount of seconds will be deleted

    );

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<TITLE>PHP-Captcha-Class :: DEMO</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

<style type="text/css">
<!--

/*********************************
 *
 *    globale HTML Styles
 *
 */
    a:link
    {
        color: #0079C5;
        background: transparent;
        text-decoration: none;
    }
    a:visited
    {
        color: #5DA3ED;
        background: transparent;
        text-decoration: none;
    }
    a:hover,
    a:active,
    a:focus
    {
        color: #cd3021;
        background: transparent;
        text-decoration: underline;
    }

    html,
    body
    {
        margin-top: 20px;
        margin-bottom: 20px;
        margin-left: 20px;
        margin-right: 20px;
        padding-top: 0px;
        padding-bottom: 0px;
        padding-left: 0px;
        padding-right: 0px;
    }

    body
    {
        background-color: #FFFFFF;
        color: #000000;
        font-family: Verdana, Helvetica, Arial, sans-serif;
    }

    h3
    {
        margin-left: 30px;
        margin-right: 20px;
        background: transparent;
        color: #222222;
        font-size: 20px;
        font-style: normal;
        font-weight: bold;
        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
        line-height: 100%;
        letter-spacing: 1px;
    }

/*********************************
 *
 *    CAPTCHA-Styles
 *
 */
    p.captcha_1,
    p.captcha_2,
    p.captcha_notvalid
    {
        margin-left: 30px;
        margin-right: 20px;
        font-size: 12px;
        font-style: normal;
        font-weight: normal;
        font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
        background: transparent;
        color: #000000;
    }
    p.captcha_2
    {
        font-size: 10px%;
        font-style: italic;
        font-weight: normal;
    }
    p.captcha_notvalid
    {
        font-weight: bold;
        color: #FFAAAA;
    }

    .captchapict
    {
        margin: 0px 0px 0px 0px;
        padding: 0px 0px 0px 0px;
        border-style: inset;
        border-width: 4px;
        border-color: #C0C0C0;
    }

    #captcha
    {
        margin-left: 30px;
        margin-right: 30px;
        border-style: dashed;
        border-width: 2px;
        border-color: #FFD940;
    }
-->
</style>
</head>
<body>
<h3>This is a demo of hn_captcha.class.php with garbage collector extension</h3>

<?PHP


    //$captcha =& new hn_captcha_X1($CAPTCHA_INIT);
    $captcha =& new hn_captcha_X1($CAPTCHA_INIT, TRUE);


    if($captcha->garbage_collector_error)
    {
        // Error! (Counter-file or deleting lost images)
        echo "<p><br><b>An ERROR has occured!</b><br>Here you might send email-notification to webmaster or something like that.</p>";
    }



    switch($captcha->validate_submit())
    {

        // was submitted and has valid keys
        case 1:
            // PUT IN ALL YOUR STUFF HERE //
                    echo "<p><br>Congratulation. You will get the resource now.";
                    echo "<br><br><a href=\"".$_SERVER['PHP_SELF']."?download=yes&id=1234\">New DEMO</a></p>";
            break;


        // was submitted with no matching keys, but has not reached the maximum try's
        case 2:
            echo $captcha->display_form();
            break;


        // was submitted, has bad keys and also reached the maximum try's
        case 3:
            //if(!headers_sent() && isset($captcha->badguys_url)) header('location: '.$captcha->badguys_url);
                    echo "<p><br>Reached the maximum try's of ".$captcha->maxtry." without success!";
                    echo "<br><br><a href=\"".$_SERVER['PHP_SELF']."?download=yes&id=1234\">New DEMO</a></p>";
            break;


        // was not submitted, first entry
        default:
            echo $captcha->display_form();
            break;

    }

?>

</body>
</html>
