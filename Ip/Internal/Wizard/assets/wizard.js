/**
 * @package ImpressPages
 *
 *
 */
(function($){
    "use strict";

    // todox: test all functions, may need to move to a global scope
    $(document).ready(function() {
        $(document).bind('initFinished.ipContentManagement', ipWizardInit);
    });

    // loading wizard content
    function ipWizardInit(event) {
        "use strict";
        $.ajax({
            type : 'GET',
            url : ip.baseUrl,
            data : {
                aa: 'Wizard.loadContent'
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
        var $tip1 = $('#ipAdminWizardTip-dragWidget'),
            $tip2 = $('#ipAdminWizardTip-dropWidget'),
            $tip3 = $('#ipAdminWizardTip-changeWidgetContent'),
            $tip4 = $('#ipAdminWizardTip-confirmWidget'),
            $tip5 = $('#ipAdminWizardTip-publish'),
            isTip1 = $tip1.length ? true : false,
            isTip2 = $tip2.length ? true : false,
            isTip3 = $tip3.length ? true : false,
            isTip4 = $tip4.length ? true : false,
            isTip5 = $tip5.length ? true : false;

        // declaring required variables
        var $allWidgets = $('.ipActionWidgetButton'),
            $firstWidget = $allWidgets.eq(0),
            $allBlocks = $('.ipBlock'),
            $block = $('.ipBlock').length ? $('.ipBlock') : false, // if main block doesn't exist we return false
            $publishButton = $('.ipAdminControls .ipActionPublish');

        // bind close on all tips
        $('.ipAdminWizardTip .ipaClose').click(function(e){
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
        if (isTip1 && $block) { // if main block doesn't exist this doesn't make sense to learn from
            $firstWidget.tooltip({
                events : { def : ',', tooltip: 'mouseenter' },
                offset : [(-$(document).scrollTop()+15),((-$firstWidget.width() / 2) - 12 - 25)],
                position: 'bottom right',
                tip : '#ipAdminWizardTip-dragWidget'
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
        if (isTip2 && $block) { // if main block doesn't exist this doesn't make sense to learn from
            $block.tooltip({
                events : { def : ',', tooltip: 'mouseenter' },
                offset : [-12,0], // touching by arrow
                position: 'top center',
                tip : '#ipAdminWizardTip-dropWidget'
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
                    offset : [(-$(document).scrollTop()+15),(($publishButton.width() / 2) + 12 + 25)],
                    position: 'bottom left',
                    tip : '#ipAdminWizardTip-publish'
                })
                .bind('click',function(event){
                    ipWizardTipDisable('publish');
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

        // binding event and action to all widgets
        $allWidgets
            .bind('dragstart',function(event,ui){
                if (isTip1) { $tip1.hide(); }
                if (isTip2) { $tip2.show(); }
                if ((isTip1 || isTip2) && $block) {
                    $block
                        .addClass('ipWizardExposeContent')
                        .expose({
                            zIndex: 998,
                            color: '#000'
                        });
                }
            })
            .bind('dragstop',function(event,ui){
                //
            })
            .bind('unsuccessfulDrop.ipWidgetButton',function(event,data){
                //data.widgetButton
                if ((isTip1 || isTip2) && $block) {
                    $.mask.close();
                    $block.removeClass('ipWizardExposeContent');
                }
                if (isTip1) { $tip1.show(); }
                if (isTip2) { $tip2.hide(); }
            })
            .bind('successfulDrop.ipWidgetButton',function(event,data){
                $tip2.hide();
                //data.widgetButton
                //data.block
                if (isTip1 || isTip2) {
                    $.mask.close();
                    $block.removeClass('ipWizardExposeContent');
                }
                if (isTip1) { ipWizardTipDisable('dragWidget'); isTip1 = false; }
                if (isTip2) { ipWizardTipDisable('dropWidget'); isTip2 = false; }
            });

        // binding events to content blocks
        $allBlocks
            .bind('deleteClick.ipBlock cancelWidget.ipWidget',function(event,data){
                if (isTip3) { $tip3.hide() }
                if (isTip4) { $tip4.hide() }
                $tip3.hide();
                $tip4.hide();
            })
            .bind('statePreview.ipWidget',function(event,data){
                if (isTip3) { ipWizardTipDisable('changeWidgetContent'); isTip3 = false; }
                if (isTip4) { ipWizardTipDisable('confirmWidget'); isTip4 = false; }
                $tip3.hide();
                $tip4.hide();
            })
            .bind('addWidget.ipWidget',function(event,data){
                var $openedWidget = $('#ipWidget-'+data.instanceId);
                var $widgetBody = $openedWidget.find('.ipaBody');
                var $removingWidget = $openedWidget.prev();
                if (isTip3) {
                    $widgetBody.tooltip({
                        events : { def : ',', tooltip: 'mouseenter' },
                        offset : [-12,0], // touching by arrow
                        position: 'top center',
                        tip : '#ipAdminWizardTip-changeWidgetContent'
                    });
                    var tip3Data = $widgetBody.data('tooltip');
                    tip3Data.show();
                    $widgetBody.bind('click',function(event){
                        ipWizardTipDisable('changeWidgetContent');
                        isTip3 = false;
                    });
                }
                if (isTip4) {
                    var $widgetConfirm = $openedWidget.find('.ipActionWidgetSave');
                    $openedWidget.find('.ipaFooter').css('position','relative'); // adding position relative for tip possitioning
                    $widgetConfirm.after($tip4); // moving tip next to confirm button
                    $widgetConfirm.tooltip({
                        events : { def : ',', tooltip: 'mouseenter' },
                        offset : [12,((-$widgetConfirm.outerWidth() / 2) - 12 - 25)],
                        position: 'bottom right',
                        tip : '#ipAdminWizardTip-confirmWidget',
                        relative : true
                    });
                    var tip4Data = $widgetConfirm.data('tooltip');
                    tip4Data.show();
                    $tip4.hide();
                    if (isTip3) {
                        $widgetBody.bind('click',function(event){
                            $tip4.show();
                        });
                    } else {
                        $tip4.show();
                    }
                    $widgetConfirm.bind('click',function(event){
                        ipWizardTipDisable('confirmWidget');
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
                aa: 'Wizard.closeWizardTip',
                id: tipId,
                securityToken: ip.securityToken
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
            // TODO: throw error
        }
    }

})(ip.jQuery);


