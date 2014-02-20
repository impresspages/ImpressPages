
var pagesZones;

(function($) {

    pagesZones = {
        init: function () {
            $('ul.ipsMenuList').sortable({
                start: this.startSort,
                stop: this.stopSort
            });
        },
        startSort: function (event, ui) {
            ui.item.data('originIndex', ui.item.index());
        },
        stopSort: function (event, ui) {
            var originIndex = ui.item.data('originIndex');
            var menuItem = ui.item;
            var newIndex = menuItem.index();

            if (originIndex == newIndex) {
                return;
            }

            if (newIndex >= originIndex) {
                newIndex++; //jsTree gives us index with removed original. Make newIndex to be as it would be preserving original position
            }

            var menuName = menuItem.data('menuname');
            var data = {};
            data.aa= 'Pages.sortZone';
            data.newIndex = newIndex;
            data.menuName = menuName;
            data.securityToken = ip.securityToken;
            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                dataType: 'json',
                success: function (response) {
                    //do nothing. Enjoy the results
                },
                error: function (response) {
                    if (ip.debugMode || ip.developmentMode) {
                        alert(response.responseText);
                    }
                }
            });
        }
    }
})(ip.jQuery);
