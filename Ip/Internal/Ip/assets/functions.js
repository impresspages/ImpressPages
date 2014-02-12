
function ipFileUrl(path)
{
    for (prefix in ipUrlOverrides) {
        if (path.indexOf(prefix) == 0) {
            return ipUrlOverrides[prefix] + path.substr(prefix.length);
        }
    }

    return ip.baseUrl + path;
}

function ipThemeUrl(path)
{
    return ipFileUrl('Theme/' + ip.theme + '/' + path);
}

function ipHomeUrl()
{
    return ip.homeUrl;
}

/**
 * retrieve a page tree (in jstree format) from backend
 *
 * @returns promise
 */
function ipGetPageTree()
{
    var data = {};
    data.aa = 'Content.getPageTree';
    data.securityToken = ip.securityToken;

    return $.ajax({
        type: 'GET',
        url: ip.baseUrl,
        data: data,
        dataType: 'json'
     });
}

/**
 * show the "browse link" modal, and call callback_function with result
 *
 * @param {Object} callback_function
 */

ipBrowseLink = function(callback_function) {
    var $=ip.jQuery,
        $modal=$('#ipBrowseLinkModal'),
        $tree=$('.ipSitemap', $modal);

    ipGetPageTree()
      .success(function (data) {
            console.log(data.sitemap);
         // init tree
         $tree.jstree({
                core: {
                    data: data.sitemap,
                    animation: 0,
                    multiple: false,
                    themes: {
                        dots: false
                    }
                },
                plugins: ['wholerow']
         });
         $tree
            .on('select_node.jstree', function () {
                 $('.btn-primary', $modal).removeClass('disabled');
            });
         // show modal
         $modal
            .modal('show')
            .on('click', '.btn-primary', function () {
               var url=$('#'+$tree.jstree('get_selected')[0]).data('url');
               $modal.modal('hide');
               $tree.jstree('destroy');
               if (callback_function) {
                   callback_function(url);
               } else {
                   console.log(url);
               }
            });
      });
}

