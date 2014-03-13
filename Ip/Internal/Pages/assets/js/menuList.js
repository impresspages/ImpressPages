
var pageMenuList;

(function($) {

    pageMenuList = {
        init: function () {
            //todox commented out because .sortable doesn't exist in unsocped jquery. restore after jquery unscoping
//            $('ul.ipsMenuList').sortable({
//                start: this.startSort,
//                stop: this.stopSort
//            });
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

            var menuId = menuItem.data('menuid');
            var data = {};
            data.aa = 'Pages.changeMenuOrder';
            data.newIndex = newIndex;
            data.menuId = menuId;
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
})(jQuery);
