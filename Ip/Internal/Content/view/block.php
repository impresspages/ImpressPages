<?php
    $showEmpty = false;
    if ($managementState && !$widgetsHtml) {
        $showEmpty = true;
    }
?>
<div id="ipBlock-<?php echo $blockName; ?>" data-revisionid='<?php echo $revisionId ?>' data-languageid='<?php echo $languageId ?>' class="ipBlock<?php echo ($showEmpty ? ' ipbEmpty' : ''); ?>"><?php

    if ($widgetsHtml) {
        foreach($widgetsHtml as $key => $widgetHtml) {
            echo $widgetHtml;
        }
    } elseif ($managementState) {
?>
        <div class="ipbExampleContent">
            <div class="ipbDefault"><?php _e('Click and type', 'Ip-admin') ?></div>
            <div class="ipbUser">
<?php
        if ($exampleContent) {
            echo $exampleContent;
        } else {
            echo ipView('Ip/Internal/Content/view/exampleContent.php')->render();
        }
?>
            </div>
        </div>
<?php
    }
?>
</div>
