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
        ?><div class="ipExampleContent"><?php echo $exampleContent; ?></div><?php
    }

?></div>
