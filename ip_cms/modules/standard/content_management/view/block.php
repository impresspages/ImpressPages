<?php
    $showEmpty = false;
    if ($managementState && !$widgetsHtml) {
        $showEmpty = true;
    }
?>
<div id="ipBlock-<?php echo $blockName; ?>" data-revisionid='<?php echo $revisionId ?>' class="ipBlock<?php echo ($showEmpty ? ' ipbEmpty' : ''); ?>"><?php

    if ($widgetsHtml) {
        foreach($widgetsHtml as $key => $widgetHtml) {
            echo $widgetHtml;
        }
    } elseif ($managementState && $exampleContent) {
        ?><div class="ipExampleContent" style="background: rgba(64, 64, 64, 0.7); opacity:0.6; filter:alpha(opacity=60)"><?php echo $exampleContent; ?></div><?php
    }

?></div>
