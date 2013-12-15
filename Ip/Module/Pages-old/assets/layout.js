/**
 * @package ImpressPages
 *
 *
 */

$(document).ready(function () {
    "use strict";
    ipPagesLayout.init();
});

var ipPagesLayout = new function () {
    "use strict";
    this.init = function () {
        $('#sideBar').resizable({
            alsoResize: '#tree'
        });
        $('#sideBar').bind('resize', fixLayout);
        $(window).bind('resize', fixLayout);
        fixLayout();
    };

    var fixLayout = function () {
        $('#pageProperties').width($(window).width() - $('#sideBar').width() - 30);
        $('#pageProperties').height($(window).height() - 48);
        $('#tree').height($(window).height() - $('#controlls').height() - 87);
        $('#sideBar').height($(window).height() - 45);
        $('#sideBar').resizable('option', 'maxHeight', $(window).height() - 25);
        $('#sideBar').resizable('option', 'minHeight', $(window).height() - 25);
        //$('#sideBar').resizable('option', 'minWidth', 354);
        $('#sideBar').resizable('option', 'maxWidth', 1600);
    };
};
