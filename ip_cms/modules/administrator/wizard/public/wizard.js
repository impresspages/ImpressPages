/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

$(document).ready(function() {
    $(document).bind('initFinished.ipContentManagement', ipWizardInit);
});

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



function ipWizardBind(data) {
    // adding required wizard HTML to the body
    $body = $('body');
    $body.append(data);

    // scrolling window to the top no matter what
    $(document).scrollTop(0);

    // bind close
    $('.ipAdminWizardTip .ipaClose').click(function(e){
        e.preventDefault();

        $.ajax({
            type : 'POST',
            url : ip.baseUrl,
            data : {
                g: 'administrator',
                m: 'wizard',
                a: 'closeWizard'
            },
            success : function(response) {
                if (response.status == 'success') {
                    window.location.reload();
                } else {
                    alert('An error occured. Reload the page. If you still see the wizard ask your administrator to turn in off manually.');
                    window.location.reload();
                }
            },
            dataType : 'json'
        });
    });

    $('.ipAdminWizardTip').slideToggle(); // hiding all tips for nice opening
    ipWizardShowStep(1); // show first step
}

function ipWizardShowStep(step) {
    $('.ipAdminWizardStep').css('display','none');
    $('#ipAdminWizardStep-'+step).css('display','block');
    window["ipWizardStep_"+step]();
}

function ipWizardStep_1() {
    // all about widget
    var $widget = $('#ipAdminWidgetButton-IpTitle');
    $widget.tooltip({
        events : { def : ',', tooltip: 'mouseenter' },
        offset : [-78,25],
        position: 'bottom right',
        tip : '#ipAdminWizardStep-1 .ipaWidget'
    });
    widgetData = $widget.data('tooltip');
    widgetData.show();
    widgetData.getTip().css('position','fixed');

    // all about block
    var $block = $('#ipBlock-main');
    var top2top = $('#ipAdminWizardStep-1 .ipaBlock').height();
    top2top = 139; // for some reason height() returns 1;
    $block.tooltip({
        events : { def : ',', tooltip: 'mouseenter' },
        offset : [top2top,-25],
        position: 'top left',
        tip : '#ipAdminWizardStep-1 .ipaBlock'
    });
    blockData = $block.data('tooltip');
    blockData.show();

    // bind playback - Step 1
    $('#ipAdminWizardStep-1 .ipaPlay').click(function(e){
        e.preventDefault();
        var $stepTips = $('#ipAdminWizardStep-1 .ipAdminWizardTip');
        var offset = $block.offset();
        var dragX = offset.left + 10;
        var distanceFromBottom = 150;
        var viewportHeight = $(window).height();
        if (viewportHeight - offset.top <= distanceFromBottom) {
            $(document).scrollTop(offset.top + distanceFromBottom - viewportHeight);
        }
        var dragY = offset.top - $(document).scrollTop() + 10;
        $widget.simulate("drag", {
            dx: dragX,
            dy: dragY,
            delayStart: 2000, // delay for 2 sec.
            delayExit: 1,
            onInit: function(){
                $stepTips.slideToggle();
            },
            onDragStart: function() { // before mousedown
                //alert('drag start');
            },
            onDragEnd: function() { // before mouseup
                //alert('drag end');
            },
            onExit: function(){
                //ipWizardShowStep(2);
                $stepTips.slideToggle();
            }
        });
    });
}

function ipWizardStep_2() {
    // all about widget
    var $block = $('#ipBlock-main');
    var top2top = 139;
    $block.tooltip({
        events : { def : ',', tooltip: 'mouseenter' },
        offset : [top2top,-25],
        position: 'top left',
        tip : '#ipAdminWizardStep-2 .ipaWidget'
    });
    blockData = $block.data('tooltip');
    blockData.show();

    // bind playback - Step 1
    $('#ipAdminWizardStep-2 .ipaPlay').click(function(e){
        e.preventDefault();
        alert('play.');
    });
}
