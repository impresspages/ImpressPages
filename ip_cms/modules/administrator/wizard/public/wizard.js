/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

"use strict";

$(document).ready(function() {
    $(document).bind('initFinished.ipContentManagement', ipWizardInit);
});

// loading wizard content
function ipWizardInit(event) {
    $.ajax({
        type : 'POST',
        url : ip.baseUrl,
        data : {
            g: 'administrator',
            m: 'wizard',
            a: 'loadContent'
        },
        success : function(response) {
            if (response.status == 'success') {
                ipWizardBind(response.content);
            }
        },
        dataType : 'json'
    });
}

// binding all events
function ipWizardBind(data) {
    // adding required wizard HTML to the body
    var $body = $('body');
    $body.append(data);

    // defining tips
    var $tip1 = $('#ipAdminWizardTip-1'),
        $tip2 = $('#ipAdminWizardTip-2'),
        $tip3 = $('#ipAdminWizardTip-3'),
        $tip4 = $('#ipAdminWizardTip-4'),
        $tip5 = $('#ipAdminWizardTip-5'),
        $tip6 = $('#ipAdminWizardTip-6'),
        isTip1 = $tip1.length ? true : false,
        isTip2 = $tip2.length ? true : false,
        isTip3 = $tip3.length ? true : false,
        isTip4 = $tip4.length ? true : false,
        isTip5 = $tip5.length ? true : false,
        isTip6 = $tip6.length ? true : false;

    // declaring required variables
    var $allWidgets = $('.ipActionWidgetButton'),
        $firstWidget = $allWidgets.eq(0),
        $allBlocks = $('.ipBlock'),
        $block = $('#ipBlock-main'),
        $publishButton = $('.ipAdminControls .ipActionPublish');

    // bind close on all tips
    $('.ipAdminWizardTip .ui-dialog-titlebar-close').click(function(e){
        e.preventDefault();
        var tipId = $(this).parent('.ipAdminWizardTip').attr('id').split('-')[1];
        ipWizardTipDisable(tipId);
    });

    // show Tip 1 on start

    // hide Tip 1 on start drag
    // show Tip 2 on start drag

    // show Tip 1 on failed drop
    // hide Tip 2 on failed drop

    // disable Tip 1 on successful drop
    // disable Tip 2 on successful drop

    // show Tip 3 on managament opened

    // disable Tip 3 on click on opened widget
    // show Tip 4 on click on opened widget

    // disable Tip 4 on "Confirm" click/state change to preview
    // show Tip 5 on "Confirm" click/state change to preview

    // disable Tip 5 on "Publish" click
    // ? show Tip 6 on "Publish" click
    // ? disable but don't hide Tip 6 on "Publish" click

    /*
     * Tip 1
     * */
    if (isTip1) {
        $firstWidget.tooltip({
            events : { def : ',', tooltip: 'mouseenter' },
            offset : [(-$(document).scrollTop()+20),((-$firstWidget.width() / 2) - 10 - 17)],
            position: 'bottom right',
            tip : '#ipAdminWizardTip-1'
        });
        var tip1Data = $firstWidget.data('tooltip');
        tip1Data.show();
        tip1Data.getTip().css('position','fixed'); // fixing position because admin panel is also fixed

        // bind playback
        $('#ipAdminWizardTip-1 .ipaPlay').click(function(e){
            e.preventDefault();
            var offset = $block.offset(),
                dragX = offset.left + 10,
                distanceFromBottom = 280,
                viewportHeight = $(window).height();
            if (viewportHeight - offset.top <= distanceFromBottom) {
                $(document).scrollTop(offset.top + distanceFromBottom - viewportHeight);
            }
            var dragY = offset.top - $(document).scrollTop() + 10;
            $firstWidget.simulate("drag", {
                dx: dragX,
                dy: dragY,
                delayStart: 1000 // delay in miliseconds
            });
        });
    }

    /*
     * Tip 2
     * */
    if (isTip2) {
        $block.tooltip({
            events : { def : ',', tooltip: 'mouseenter' },
            offset : [-17,0], // touching by arrow
            position: 'top center',
            tip : '#ipAdminWizardTip-2'
        });
        var tip2Data = $block.data('tooltip');
        tip2Data.show();
        $tip2.hide();
    }

    /*
     * Tip 3
     * */
    // bind to opened widget

    /*
     * Tip 4
     * */
    // bind to opened widget

    /*
     * Tip 5
     * */
    if (isTip5) {
        $publishButton
        .tooltip({
            cancelDefault : false,
            events : { def : ',', tooltip: 'mouseenter' },
            offset : [(-$(document).scrollTop()+20),(($publishButton.width() / 2) + 10 + 17)],
            position: 'bottom left',
            tip : '#ipAdminWizardTip-5'
        })
        .bind('click',function(event){
            ipWizardTipDisable(5);
            isTip5 = false;
        });
        var tip5Data = $publishButton.data('tooltip');
        tip5Data.show();
        tip5Data.getTip().css('position','fixed');
        if (!isTip4) {
            $tip5.show();
        } else {
            $tip5.hide();
        }
    }

    /*
     * Tip 6
     * */
    // undefined


    // binding event and action to all widgets
    $allWidgets
    .bind('dragstart',function(event,ui){
        if (isTip1) { $tip1.hide(); }
        if (isTip2) { $tip2.show(); }
        if (isTip1 || isTip2) {
            $block.expose({
                zIndex: 998,
                color: '#000'
            });
        }
    })
    .bind('dragstop',function(event,ui){
        if (isTip1 || isTip2) {
            $.mask.close();
        }
    })
    .bind('unsuccessfulDrop.ipWidgetButton',function(event,data){
        //data.widgetButton
        if (isTip1) { $tip1.show(); }
        if (isTip2) { $tip2.hide(); }
    })
    .bind('successfulDrop.ipWidgetButton',function(event,data){
        //data.widgetButton
        //data.block
        if (isTip1) { ipWizardTipDisable(1); isTip1 = false; }
        if (isTip2) { ipWizardTipDisable(2); isTip2 = false; }
    });

    // binding events to content blocks
    $allBlocks
    .bind('statePreview.ipWidget',function(event,data){
        if (isTip3) { ipWizardTipDisable(3); isTip3 = false; }
        if (isTip4) { ipWizardTipDisable(4); isTip4 = false; }
    })
    .bind('stateManagement.ipWidget',function(event,data){
        var $openedWidget = $('#ipWidget-'+data.instanceId);
        var $removingWidget = $openedWidget.prev();
        if (isTip3) {
            var $widgetBody = $openedWidget.find('.ipaBody');
            $widgetBody.tooltip({
                events : { def : ',', tooltip: 'mouseenter' },
                offset : [(-$removingWidget.outerHeight(true)-17),0], // touching by arrow
                position: 'top center',
                tip : '#ipAdminWizardTip-3'
            });
            var tip3Data = $widgetBody.data('tooltip');
            tip3Data.show();
            $openedWidget.bind('click',function(event){
                ipWizardTipDisable(3);
                isTip3 = false;
            });
        }
        if (isTip4) {
            var $widgetConfirm = $openedWidget.find('.ipActionWidgetSave');
            $widgetConfirm.tooltip({
                events : { def : ',', tooltip: 'mouseenter' },
                offset : [(-$removingWidget.outerHeight(true)+17),((-$widgetConfirm.outerWidth() / 2) - 10 - 17)],
                position: 'bottom right',
                tip : '#ipAdminWizardTip-4'
            });
            var tip4Data = $widgetConfirm.data('tooltip');
            tip4Data.show();
            $tip4.hide();
            if (isTip3) {
                $openedWidget.bind('click',function(event){
                    $tip4.show();
                });
            } else {
                $tip4.show();
            }
            $widgetConfirm.bind('click',function(event){
                ipWizardTipDisable(4);
                isTip4 = false;
            });
        }
        if (isTip5) {
            var $widgetConfirm = $openedWidget.find('.ipActionWidgetSave');
            $widgetConfirm.bind('click',function(event){
                $tip5.show();
            });
        }
    });

}

function ipWizardTipDisable(tipId) {
    $.ajax({
        type : 'POST',
        url : ip.baseUrl,
        data : {
            g: 'administrator',
            m: 'wizard',
            a: 'closeWizardTip',
            id: tipId
        },
        success : ipWizardTipDisableResponse,
        dataType : 'json'
    });
}

function ipWizardTipDisableResponse(response) {
    if (response.status == 'success') {
        var tipId = response.tipId;
        // additional actions to remove tip
        $('#ipAdminWizardTip-'+tipId).remove();
    } else {
        alert('An error occured. Reload the page. If you still see the wizard ask your administrator to turn in off manually.');
    }
}
