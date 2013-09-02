$(document).ready(function () {
    $(document).bind('initFinished.ipContentManagement', function () {
        $('.ipActionWidgetButton')
            .bind('dragstart', function (event, ui) {

                $('.ipBlock > .ipExampleContent').each(function () {
                    $ipExampleContent = $(this);
                    var $block = $ipExampleContent.parent();

                    if ($block.css('min-height')) {
                        // save block min-height in order to restore it
                        $block.data('ipMinHeight', $block.css('min-height'));
                    }

                    $block.css('min-height', $block.height());

                    // $ipExampleContent.css('visibility', 'hidden').css('position', 'absolute');
                    $ipExampleContent.fadeOut('slow');
                });
            })
            .bind('dragstop', function (event, ui) {
                $('.ipBlock > .ipExampleContent').each(function () {
                    $ipExampleContent = $(this);
                    var $block = $ipExampleContent.parent();
                    if ($block.children('.ipAdminWidgetPlaceholder').length) {
                        $ipExampleContent.remove();
                    } else {
                        // $ipExampleContent.css('visibility', 'visible').css('position', 'static')
                        $ipExampleContent.fadeIn('fast');
                    }

                    if (!$block.data('ipMinHeight')) { // block had no min-height before
                        $block.css('min-height', '');
                    } else {
                        $block.css('min-height', $block.data('ipMinHeight'));
                        $block.removeData('ipMinHeight');
                    }
                });
            });
    });
});