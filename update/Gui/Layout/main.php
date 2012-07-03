<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>'.IP_INSTALLATION.'</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="robots" content="NOINDEX,NOFOLLOW">
  <link href="design/style.css" rel="stylesheet" type="text/css" />  
  <link rel="SHORTCUT ICON" href="favicon.ico" />
</head>   
<body>

  <div class="container">
    <img id="logo" src="design/cms_logo.gif" alt="ImpressPages CMS" />
    <div class="clear"></div>
    <div id="wrapper">
      <p id="installationNotice">'.IP_INSTALLATION.'</span></p>
      <div class="clear"></div>
      <img class="border" src="design/cms_main_top.gif" alt="Design" />
      <div id="main">
        <div id="menu">
        '.HtmlOutput::generateMenu().'
        </div>
        <div id="content">    
            <?php echo $content ?>
        </div>
        <div class="clear"></div>
      </div>
      <img class="border" src="design/cms_main_bottom.gif" alt="Design" />
      <div class="clear"></div>
    </div>
    <div class="footer">Copyright 2009-'.date("Y").' by <a href="http://www.impresspages.org">ImpressPages LTD</a></div>
  </div>
    
    <script type="text/javascript">
        <!--
        if (document.images) {
            preload_image = new Image(); 
            preload_image.src="design/cms_button_hover.gif"; 
        }
        //-->
    </script>

</body>