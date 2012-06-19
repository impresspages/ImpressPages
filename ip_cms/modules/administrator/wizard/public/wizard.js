/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

$(document).ready(function() {
    $(document).bind('initFinished.ipContentManagement', ipWizardInit);

/*
    // global variable to keep states of all widgets
    var widgetsState = new Array();

    // defining default state of all widgets
    $('.ipBlock').each(function(){
        $(this).find('.ipWidget').each(function(){
            var $widget = $(this);
            widgetsState[$widget.attr('id')] = $widget.hasClass('ipAdminWidget') ? 'admin' : 'preview';
        });
    });

    // widget confirmed/canceled (new or edited)
    $('.ipBlock').bind('reinitRequired.ipWidget', function(event) {
        var $block = $(event.currentTarget);
        $block.find('.ipWidget').each(function(){
            var $widget = $(this),
                widgetId = $widget.attr('id'),
                state = $widget.hasClass('ipAdminWidget') ? 'admin' : 'preview',
                regexp = /ip(Admin|)Widget\-(.*)\s+/g,
                found = regexp.exec($widget.attr('class')),
                type = found[2];
            if (state != widgetsState[widgetId]) { // state has been changed
                //console.log('Event: modified; Block: #'+$block.attr('id')+'; Widget: #'+widgetId+'; State: '+state+'; Type: '+type);
                alert('Event: modified; Block: #'+$block.attr('id')+'; Widget: #'+widgetId+'; State: '+state+'; Type: '+type);
                widgetsState[widgetId] = state; // declaring new state
            }
        });
    });

    // widget deleted
    $('.ipBlock').bind('deleteWidget.ipBlock', function(event, instanceId) {
        var $block = $(event.currentTarget),
            widgetId = 'ipWidget-'+instanceId;
            $widget = $('#'+widgetId),
            state = $widget.hasClass('ipAdminWidget') ? 'admin' : 'preview',
            regexp = /ip(Admin|)Widget\-(.*)\s+/g,
            found = regexp.exec($widget.attr('class')),
            type = found[2];
        //console.log('Event: deleted; Block: #'+$block.attr('id')+'; Widget: #'+widgetId+'; State: '+state+'; Type: '+type);
        alert('Event: deleted; Block: #'+$block.attr('id')+'; Widget: #'+widgetId+'; State: '+state+'; Type: '+type);
    });
*/
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

    ipWizardStep_init();
}

function ipWizardShowStep(step) {
    $('.ipAdminWizardStep').css('display','none');
    $('#ipAdminWizardStep-'+step).css('display','block');
    window["ipWizardStep_"+step]();
}

function ipWizardStep_init() {
    $init = $("#ipAdminWizardStep-init");
    $init.overlay({
        top: 'center',
        mask: {
            color: '#fff',
            loadSpeed: 200,
            opacity: 0.5
        },
        closeOnClick: false,
        load: true,
        close: 'ipaStart'
    });
    var initOverlay = $init.data('overlay');
    $init.find('.ipaStart').click(function(e){
        e.preventDefault();
        initOverlay.close();
        ipWizardShowStep(1);
    });
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
    var widgetData = $widget.data('tooltip');
    widgetData.show();
    widgetData.getTip().css('position','fixed');

    // all about block
    var $block = $('#ipBlock-main');
    var top2top = $('#ipAdminWizardStep-1 .ipaBlock').height();
    $block.tooltip({
        events : { def : ',', tooltip: 'mouseenter' },
        offset : [top2top,-25],
        position: 'top left',
        tip : '#ipAdminWizardStep-1 .ipaBlock'
    });
    var blockData = $block.data('tooltip');
    blockData.show();

    // bind playback
    $('#ipAdminWizardStep-1 .ipaPlay').click(function(e){
        e.preventDefault();
        var $stepTips = $('#ipAdminWizardStep-1 .ipAdminWizardTip');
        var offset = $block.offset();
        var dragX = offset.left + 10;
        var distanceFromBottom = 280;
        var viewportHeight = $(window).height();
        if (viewportHeight - offset.top <= distanceFromBottom) {
            $(document).scrollTop(offset.top + distanceFromBottom - viewportHeight);
        }
        var dragY = offset.top - $(document).scrollTop() + 10;
        $widget.simulate("drag", {
            dx: dragX,
            dy: dragY,
            delayStart: 1000, // delay in miliseconds
            //delayExit: 0, // delay in miliseconds
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
                $stepTips.slideToggle();
            }
        });
    });
    // bind next
    $('#ipAdminWizardStep-1 .ipaNext').click(function(e){
        e.preventDefault();
        var $firstWidget = $block.find('.ipWidget').eq(0);
        if ($firstWidget.hasClass('ipAdminWidget-IpTitle')) {
            ipWizardShowStep(2);
        } else {
            alert('You didn\'t finish Step 1. \n\nFirstly, drag a "Title" widget to the "Main" content area. \nOr click "Play it" button to do it automatically.');
        }
    });
}

function ipWizardStep_2() {
    // all about widget
    var $widget = $('#ipBlock-main').find('.ipWidget').eq(0);
    var top2top = $('#ipAdminWizardStep-2 .ipaWidget').height();
    $widget.tooltip({
        events : { def : ',', tooltip: 'mouseenter' },
        offset : [top2top,-25],
        position: 'top left',
        tip : '#ipAdminWizardStep-2 .ipaWidget'
    });
    var widgetData = $widget.data('tooltip');
    widgetData.show();

    // all about buttons
    var $buttons = $widget.find('.ipaFooter');
    var top2top = $('#ipAdminWizardStep-2 .ipaButtons').height();
    $buttons.tooltip({
        events : { def : ',', tooltip: 'mouseenter' },
        offset : [top2top,25],
        position: 'top right',
        tip : '#ipAdminWizardStep-2 .ipaButtons'
    });
    var buttonsData = $buttons.data('tooltip');
    buttonsData.show();

    // bind playback
    $('#ipAdminWizardStep-2 .ipaPlay').click(function(e){
        e.preventDefault();
        // inserting text
        $widget.find('.ipaBody .ipAdminInput').val('My first title!');
        // hitting 'Confirm'
        $buttons.find('.ipActionWidgetSave').click();
        // autoforward to Step 3
        ipWizardShowStep(3);
    });

    // bind next
    $('#ipAdminWizardStep-2 .ipaNext').click(function(e){
        e.preventDefault();
        ipWizardShowStep(3);
    });
}

function ipWizardStep_3() {
    // all about buttons
    var $buttons = $('.ipAdminPanel .ipAdminControls');
    var top2top = $('#ipAdminWizardStep-3 .ipaButtons').height()-$(document).scrollTop()+65;
    $buttons.tooltip({
        events : { def : ',', tooltip: 'mouseenter' },
        offset : [top2top,-25],
        position: 'top left',
        tip : '#ipAdminWizardStep-3 .ipaButtons'
    });
    var buttonsData = $buttons.data('tooltip');
    buttonsData.show();
    buttonsData.getTip().css('position','fixed');

    // bind next
    $('#ipAdminWizardStep-3 .ipaPlay').click(function(e){
        e.preventDefault();
        // hitting 'Confirm'
        $buttons.find('.ipActionPublish').click();
    });
}
