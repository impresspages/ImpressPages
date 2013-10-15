/**
 * @package ImpressPages
 *
 *
 */
function IpWidget_IpTitle(widgetObject) {
    this.widgetObject = widgetObject;

    this.manageInit = function () {
        var $self=this.widgetObject;
        $self.find('.ipTitleOptionsButton').on('click', function (e) {
            $self.find('.ipTitleOptions').toggle();
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    }
};